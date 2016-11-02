<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Invoices\Invoice;
use Ntech\Invoices\InvoicePaymentTerms;
use Ntech\Invoices\InvoiceReferenceInformation;
use Ntech\Invoices\Items\InvoiceItemCollection;
use Ntech\Invoices\Items\Item;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Subscriptions\Subscription;
use Ntech\Subscriptions\SubscriptionsRepository;
use Ntech\Uuid\Uuid;
use Ntech\Uuid\UuidGenerator;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class GenerateInvoiceForSubscriptionPeriodHandler extends CommandHandler
{
    /**
     * @var SubscriptionsRepository
     */
    private $subscriptionsRepository;
    /**
     * @var EventSourcingRepository
     */
    private $invoiceSourceRepository;
    /**
     * @var EventSourcingRepository
     */
    private $subscriptionSourceRepository;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory,
        SubscriptionsRepository $subscriptionsRepository
    ) {
        $this->subscriptionsRepository = $subscriptionsRepository;
        $this->invoiceSourceRepository = $sourceFactory->forAggregate(Invoice::class);
        $this->subscriptionSourceRepository = $sourceFactory->forAggregate(Subscription::class);
    }
    
    public function handle(GenerateInvoiceForSubscriptionPeriodCommand $command)
    {
        $period = $command->getSubscriptionPeriod();

        /** @var SubscriptionDoctrineModel $subscriptionModel */
        $subscriptionModel = $this->subscriptionsRepository->getSubscription($period->getSubscriptionId());

        if ($subscriptionModel->isPrepay()) {
            $amount = $subscriptionModel->getAmount();

            $invoiceReferenceInformation = new InvoiceReferenceInformation([
                'title' => 'Invoice for ' . $subscriptionModel->getName(),
                'description' => 'Due payment for subscription ' . $subscriptionModel->getName() .
                    ' for period ' . $period->getStartDate()->format('jS F, Y') . ' - ' . $period->getEndDate()->format('jS F, Y')
            ]);

            $invoiceTerms = new InvoicePaymentTerms([
                'daysUntilDue' => 0,
                'invoiceDate' => $period->getStartDate()
            ]);
            $invoiceItems = new InvoiceItemCollection([
                new Item(
                    Uuid::uuid4(),
                    1,
                    'Subscription Due',
                    '',
                    $subscriptionModel->getAmount(),
                    1
                )
            ]);
                
            $invoice = Invoice::create(
                $command->getInvoiceId(),
                $subscriptionModel->getCompanyId(),
                $subscriptionModel->getCustomerId(),
                $invoiceReferenceInformation,
                $invoiceTerms,
                $invoiceItems
            );

            $subscription = $this->subscriptionSourceRepository->load($period->getSubscriptionId());

            $invoice->assignToEntity($subscription);
            
            $this->invoiceSourceRepository->save($invoice);
        }
    }
}
