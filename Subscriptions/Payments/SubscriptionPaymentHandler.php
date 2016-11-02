<?php
namespace Ntech\Subscriptions\Payments;

use Ntech\CommandBus\CommandBus;
use Ntech\Exceptions\DomainException;
use Ntech\Invoices\Invoice;
use Ntech\Invoices\InvoiceRepository;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel;
use Ntech\Payments\Commands\AssignPaymentCommand;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineModel;
use Ntech\Payments\Payment;
use Ntech\Payments\PaymentsRepository;
use Ntech\Subscriptions\SubscriptionsRepository;
use Ntech\Uuid\Uuid;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class SubscriptionPaymentHandler
{
    /**
     * @var PaymentsRepository
     */
    private $paymentsRepository;
    /**
     * @var SubscriptionsRepository
     */
    private $subscriptionsRepository;
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;
    /**
     * @var EventSourcingRepository
     */
    private $paymentSourceRepository;
    /**
     * @var InvoiceSourceRepository
     */
    private $invoiceSourceRepository;
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory,
        PaymentsRepository $paymentsRepository,
        SubscriptionsRepository $subscriptionsRepository,
        InvoiceRepository $invoiceRepository,
        CommandBus $commandBus
    ) {
        $this->paymentsRepository = $paymentsRepository;
        $this->subscriptionsRepository = $subscriptionsRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentSourceRepository = $sourceFactory->forAggregate(Payment::class);
        $this->invoiceSourceRepository = $sourceFactory->forAggregate(Invoice::class);
        $this->commandBus = $commandBus;
    }

    public function handlePayment(Uuid $subscriptionId, Uuid $paymentId)
    {
        // Payment has been assigned to a subscription
        // Sort out applying that payment to an invoice

        $invoices = $this->subscriptionsRepository->getSubscriptionInvoices($subscriptionId);
        if ($invoices->count() == 0) {
            // For some reason no invoices have been generated for the subscription even though
            // a payment has been made (this might happen if we get automated payment from a gateway
            // before we've generated an invoice ourselves)
            // In this case just exit and let the cleanup cron job hand re-attempting to sort
            // the payment
            throw DomainException::because("There are no invoices for this Subscription. Have we received payment early?");
        }
        // Grab the latest subscription invoice
        $latestInvoice = $invoices->getLatest();

        /** @var PaymentDoctrineModel $paymentModel */
        $paymentModel = $this->paymentsRepository->getSinglePaymentModel($paymentId);

        // Only proceed if the latest invoice is unpaid. Any other status and something isnt quite right (see else)
        if ($latestInvoice->getStatus() == Invoice::STATUS_UNPAID) {
            // We only automatically handle assignment if the invoice and payment amount is identical
            $this->assignPaymentToSubscriptionInvoice($latestInvoice, $paymentModel);
        } else {
            throw DomainException::because("Trying to pay Subscription Invoice but the Invoice status is not UNPAID, it is [{$latestInvoice->getStatus()}].");
            // If the latest invoice is already paid then we've either received a duplicate payment
            // or we havnt generated the the relevent invoice yet.
            // If the invoice is void then the subscription will have been cancelled and we shouldnt
            // be getting a payment at all so throw an exception
        }
    }

    private function assignPaymentToSubscriptionInvoice(
        InvoiceDoctrineModel $latestInvoice,
        PaymentDoctrineModel $paymentModel
    ) {
        if ($latestInvoice->getTotal() != $paymentModel->getAmount()) {
            throw DomainException::because(
                "Payment amount [{$paymentModel->getAmount()->readable()}] 
                differs from Invoice amount [{$latestInvoice->getTotal()->readable()}]."
            );
        }
        // Amounts match, all seems good lets assign the payment to the invoice
        // but double check the payment hasnt been assigned to anything first
        if ($paymentModel->isAssigned()) {
            throw DomainException::because("Trying to assign Payment to a Subscription invoice but Payment has already been assigned to something.");
        }
        /** @var Payment $payment */
        $payment = $this->paymentSourceRepository->load($paymentModel->getId());
        /** @var Invoice $invoice */
        $invoice = $this->invoiceSourceRepository->load($latestInvoice->getId());

        $assignPayment = new AssignPaymentCommand(
            $paymentModel->getId(),
            $latestInvoice->getId(),
            Invoice::class
        );
        $this->commandBus->handle($assignPayment);
    }
}
