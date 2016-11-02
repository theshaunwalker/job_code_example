<?php
namespace Ntech\Subscriptions\Models\SubscriptionProductSignupFlow;

use Doctrine\ORM\EntityManager;
use Ntech\Subscriptions\Events\SubscriptionProductSignupFlow\SubscriptionProductFlowCreated;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel;
use Ntech\Uuid\UuidGenerator;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;

class SubscriptionProductSignupFlowProjector extends AbstractProjector
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var UuidGenerator
     */
    private $uuidGenerator;

    public function __construct(
        EntityManager $entityManager,
        UuidGenerator $uuidGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->uuidGenerator = $uuidGenerator;
    }
    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            SubscriptionProductFlowCreated::class
        ];
    }

    /**
     * Logic for deleting all projected data from this projection
     */
    public function delete()
    {
        $this->entityManager->getConnection()->exec("TRUNCATE subscriptions_products_signup_flows;");
    }

    public function projectSubscriptionProductFlowCreated(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductFlowCreated $event */
        $event = $domainEvent->getPayload();

        $flow = new SubscriptionProductSignupFlowModel(
            $event->getFlowId(),
            $this->entityManager->getReference(SubscriptionProductModel::class, $event->getSubscriptionProductId()->toString()),
            $event->getFlowReference(),
            $event->getSignupSettings()
        );
        $this->entityManager->persist($flow);
        $this->entityManager->flush();
    }
}
