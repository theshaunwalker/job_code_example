<?php
namespace Ntech\Subscriptions\Events\Listeners;

use Ntech\Invoices\Invoice;
use Ntech\Invoices\InvoicePaymentTerms;
use Ntech\Invoices\InvoiceReferenceInformation;
use Ntech\Invoices\Items\InvoiceItemCollection;
use Ntech\Invoices\Items\Item;
use Ntech\Subscriptions\Events\SubscriptionPeriodEnded;
use Ntech\Subscriptions\Events\SubscriptionPeriodStarted;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Subscriptions\Subscription;
use Ntech\Subscriptions\SubscriptionsRepository;
use Ntech\Uuid\Uuid;
use Ntech\Uuid\UuidGenerator;
use NtechUtility\EventSource\Domain\DomainEvent;
use NtechUtility\EventSource\EventBus\AbstractDomainEventListener;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

/**
 * Listens to subscription periods starting/ending to create the relevant
 * subscription dues that will need to be paid.
 * Pre-pay subscriptions have dues generated on SubscriptionPeriodStarted
 * Usage based subscriptions have dues generated on SubscriptionPeriodEnded as
 * we can only calculate usage after the fact
 */
class GenerateSubscriptionDues extends AbstractDomainEventListener
{

    /**
     * @var SubscriptionsRepository
     */
    private $subscriptionsRepository;

    private $subscriptionSourceRepository;

    private $invoiceSourceRepository;
    /**
     * @var UuidGenerator
     */
    private $uuidGenerator;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory,
        SubscriptionsRepository $subscriptionsRepository,
        UuidGenerator $uuidGenerator
    ) {
        $this->subscriptionsRepository = $subscriptionsRepository;
        $this->subscriptionSourceRepository = $sourceFactory->forAggregate(Subscription::class);
        $this->invoiceSourceRepository = $sourceFactory->forAggregate(Invoice::class);
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * Array of event class names that this listener listens for
     * @return array
     */
    public function eventsListeningFor() : array
    {
        return [
            SubscriptionPeriodStarted::class,
            SubscriptionPeriodEnded::class
        ];
    }

    public function listenSubscriptionPeriodStarted(DomainEvent $domainEvent)
    {
        /** @var SubscriptionPeriodStarted $event */
        $event = $domainEvent->getPayload();
        /** @var SubscriptionDoctrineModel $subscriptionModel */
        $subscriptionModel = $this->subscriptionsRepository->getSubscription($event->getSubscriptionId());

        // We only generate anything if its a prepay subscription
        if ($subscriptionModel->isPrepay()) {
            /** @var Subscription $subscription */
            $subscription = $this->subscriptionSourceRepository->load($event->getSubscriptionId());
            $subscriptionPeriod = $event->getPeriod();

            $subscription->duesNeededForPeriod($subscriptionPeriod);
            $this->subscriptionSourceRepository->save($subscription);

            /**
             * Generate Invoice to go with the dues
             */
            $dueModel = $this->subscriptionsRepository->getSubscriptionDueForPeriod(
                $subscriptionModel->getId(),
                $subscriptionPeriod->getOrderCount()
            );
            $dueAmount = $dueModel->getAmount();
            $invoiceDate = $subscriptionModel->isPrepay() == true ?
                $subscriptionPeriod->getStartDate() : $subscriptionPeriod->getEndDate();

            $dueInvoice = Invoice::create(
                $this->uuidGenerator->uuid4(),
                $subscriptionModel->getCompanyId(),
                $subscriptionModel->getCustomerId(),
                new InvoiceReferenceInformation([
                    'title' => "Subscription fee for " . $subscriptionModel->getName() . ' #' . $subscriptionPeriod->getOrderCount(),
                    'description' => 'Subscription dues for period between ' .
                        $subscriptionPeriod->getStartDate()->format('jS F, Y') . ' - ' .
                        $subscriptionPeriod->getEndDate()->format('jS F, Y')
                ]),
                new InvoicePaymentTerms([
                    'daysUntilDue' => 0,
                    'invoiceDate' => $invoiceDate
                ]),
                new InvoiceItemCollection([
                    new Item(
                        Uuid::uuid4(),
                        1,
                        'Subscription fee',
                        '',
                        $dueAmount,
                        1
                    )
                ])
            );
            $dueInvoice->assignToEntity($subscription);
            $this->invoiceSourceRepository->save($dueInvoice);

            $subscription->setDueInvoice($subscriptionPeriod->getOrderCount(), $dueInvoice);
            $this->subscriptionSourceRepository->save($subscription);
        }
    }

    public function listenSubscriptionPeriodEnded(DomainEvent $domainEvent)
    {
        /** @var SubscriptionPeriodEnded $event */
        $event = $domainEvent->getPayload();

        /** @var SubscriptionDoctrineModel $subscriptionModel */
        $subscriptionModel = $this->subscriptionsRepository->getSubscription($event->getSubscriptionId());

        // If subscription is NOT prepay we need to calculate usages
        if (!$subscriptionModel->isPrepay()) {
            // Calculate usages that need to be invoiced here
            // This currently isnt implemented
        }
    }
}
