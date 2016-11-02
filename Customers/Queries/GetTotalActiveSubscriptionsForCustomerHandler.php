<?php
namespace Ntech\Customers\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetTotalActiveSubscriptionsForCustomerHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function handle(GetTotalActiveSubscriptionsForCustomerQuery $query)
    {
        $totalCount = $this->entityManager->getRepository(SubscriptionDoctrineModel::class)
            ->createQueryBuilder('sub')
            ->select('count(sub.id)')
            ->where('sub.customer = :customer')
            ->setParameters([
                'customer' => $query->getCustomerId()->toString()
            ])
            ->getQuery()
            ->getSingleScalarResult();
        return $totalCount;
    }
}
