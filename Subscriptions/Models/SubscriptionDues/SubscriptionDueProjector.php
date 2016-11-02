<?php
namespace Ntech\Subscriptions\Models\SubscriptionDues;

use Doctrine\ORM\EntityManager;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel;
use Ntech\Subscriptions\Events\SubscriptionDueGeneratedForPeriod;
use Ntech\Subscriptions\Events\SubscriptionDueInvoiceGenerated;
use Ntech\Subscriptions\SubscriptionsRepository;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;

class SubscriptionDueProjector extends AbstractProjector
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var SubscriptionsRepository
     */
    private $subscriptionsRepository;

    public function __construct(
        EntityManager $entityManager,
        SubscriptionsRepository $subscriptionsRepository
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionsRepository = $subscriptionsRepository;
    }

    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            SubscriptionDueGeneratedForPeriod::class,
            SubscriptionDueInvoiceGenerated::class
        ];
    }

    /**
     * Logic for deleting all projected data from this projection
     */
    public function delete()
    {
        $this->entityManager->getConnection()->exec("TRUNCATE subscriptions_dues;");
    }

    public function projectSubscriptionDueGeneratedForPeriod(DomainEvent $domainEvent)
    {
        /** @var SubscriptionDueGeneratedForPeriod $event */
        $event = $domainEvent->getPayload();

        $dueModel = new SubscriptionDueModel(
            $event->getSubscriptionId(),
            $event->getPeriodId(),
            $event->getDue()
        );

        $this->entityManager->persist($dueModel);
        $this->entityManager->flush();
    }
    
    public function projectSubscriptionDueInvoiceGenerated(DomainEvent $domainEvent)
    {
        /** @var SubscriptionDueInvoiceGenerated $event */
        $event = $domainEvent->getPayload();

        $dueModel = $this->subscriptionsRepository->getSubscriptionDueForPeriod(
            $event->getSubscriptionId(),
            $event->getPeriodId()
        );

        $dueModel->setInvoice(
            $this->entityManager->getReference(InvoiceDoctrineModel::class, $event->getInvoiceId()->toString())
        );
        
        $this->entityManager->persist($dueModel);
        $this->entityManager->flush();

    }
}
