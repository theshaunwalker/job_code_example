<?php
namespace Ntech\Subscriptions\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Subscriptions\Exceptions\SubscriptionDoesNotExist;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetSubscriptionHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionRepository = $this->entityManager->getRepository(SubscriptionDoctrineModel::class);
    }
    public function handle(GetSubscriptionQuery $query)
    {
        $subscription = $this->subscriptionRepository->find($query->getSubscriptionId()->toString());
        if ($subscription == null) {
            throw SubscriptionDoesNotExist::because("No Subscription with that ID");
        }
        return $subscription;
    }
}
