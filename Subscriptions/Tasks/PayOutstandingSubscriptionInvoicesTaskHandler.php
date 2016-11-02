<?php
namespace Ntech\Subscriptions\Tasks;

use Doctrine\ORM\EntityManager;
use Ntech\CommandBus\CommandBus;
use Ntech\Invoices\Commands\PayAnInvoiceCommand;
use Ntech\Invoices\Exceptions\CannotPayInvoice;
use Ntech\Invoices\Invoice;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel;
use Ntech\Invoices\Queries\FindInvoicesForCustomerQuery;
use Ntech\Payments\Commands\AssignPaymentCommand;
use Ntech\Payments\Commands\CapturePaymentCommand;
use Ntech\Payments\Commands\GeneratePaymentCommand;
use Ntech\Payments\Methods\SavedPaymentMethod;
use Ntech\Payments\Processing\Webhooks\PaymentServiceLocator;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Subscriptions\Queries\GetSubscriptionQuery;
use Ntech\Subscriptions\Subscription;
use Ntech\Uuid\UuidGenerator;
use NtechUtility\Cqrs\Query\QueryProcessor;

class PayOutstandingSubscriptionInvoicesTaskHandler
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var PaymentServiceLocator
     */
    private $paymentServiceLocator;
    /**
     * @var UuidGenerator
     */
    private $uuidGenerator;

    public function __construct(
        CommandBus $commandBus,
        QueryProcessor $queryProcessor,
        EntityManager $entityManager,
        PaymentServiceLocator $paymentServiceLocator,
        UuidGenerator $uuidGenerator
    ) {
        $this->commandBus = $commandBus;
        $this->queryProcessor = $queryProcessor;
        $this->entityManager = $entityManager;
        $this->paymentServiceLocator = $paymentServiceLocator;
        $this->uuidGenerator = $uuidGenerator;
    }
    
    public function handle(PayOutstandingSubscriptionInvoicesTask $task)
    {
        /** @var SubscriptionDoctrineModel $subscription */
        $subscription = $this->queryProcessor->process(
            new GetSubscriptionQuery($task->getSubscriptionId())
        );
        
        $subscriptionPaymentMethod = $subscription->getPaymentMethod();
        if ($subscriptionPaymentMethod == null) {
            throw CannotPayInvoice::because("Subscription does not have a saved payment method.");
        }

        $invoices = $this->entityManager->getRepository(InvoiceDoctrineModel::class)
            ->createQueryBuilder('invoice')
            ->where('invoice.entityClass = :entityClass')
            ->andWhere('invoice.entityId = :entityId')
            ->andWhere('invoice.status = :status')
            ->andWhere('invoice.dueDate <= CURRENT_DATE()')
            ->setParameters([
                'entityClass' => Subscription::class,
                'entityId' => $task->getSubscriptionId()->toString(),
                'status' => Invoice::STATUS_UNPAID
            ])
            ->getQuery()
            ->getResult();

        if (empty($invoices)) {
            return;
        }

        $paymentService = $this->paymentServiceLocator->byCompanyId(
            $subscriptionPaymentMethod->getSavedPaymentMethod()->getMethodKey(),
            $subscriptionPaymentMethod->getCompanyId()
        );

        /** @var SavedPaymentMethod $savedMethod */
        $savedMethod = $paymentService->generateSavedMethodObject($subscriptionPaymentMethod->getSavedPaymentMethod());

        /** @var InvoiceDoctrineModel $invoice */
        foreach ($invoices as $invoice) {
            // Make the payment and capture it
            $paymentId = $this->uuidGenerator->uuid4();
            $generatePayment = new GeneratePaymentCommand(
                $paymentId,
                $invoice->getCompanyId(),
                $invoice->getCustomerId(),
                $savedMethod->getMethodKey(),
                $invoice->getOutstandingBalance()
            );
            $this->commandBus->handle($generatePayment);
            $capturePayment = new CapturePaymentCommand(
                $paymentId,
                $paymentService,
                $savedMethod
            );
            $this->commandBus->handle($capturePayment);
            // Assign payment to invoice
            $assignPayment = new AssignPaymentCommand(
                $paymentId,
                $invoice->getId(),
                Invoice::class
            );
            $this->commandBus->handle($assignPayment);
        }
    }
}
