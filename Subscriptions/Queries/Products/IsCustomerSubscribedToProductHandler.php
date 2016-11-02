<?php
namespace Ntech\Subscriptions\Queries\Products;

use Doctrine\ORM\EntityManager;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel;
use NtechUtility\Cqrs\Query\QueryHandler;

class IsCustomerSubscribedToProductHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $queryBuilder;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->queryBuilder = $entityManager->getRepository(SubscriptionDoctrineModel::class)->createQueryBuilder('subscription');
    }
    
    public function handle(IsCustomerSubscribedToProductQuery $query)
    {
        $this->queryBuilder
            ->select('count(subscription)')
            ->where('subscription.subscriptionProduct = :subscriptionProduct')
            ->andWhere('subscription.customer = :customer');
        $this->queryBuilder->setParameters([
            'subscriptionProduct' =>
                $this->entityManager->getReference(SubscriptionProductModel::class, $query->getSubscriptionProductId()->toString()),
            'customer' =>
                $this->entityManager->getReference(CustomerDoctrineModel::class, $query->getCustomerId()->toString())
        ]);

        $result = (int)$this->queryBuilder->getQuery()->getSingleScalarResult();
        return $result > 0;
    }
}
