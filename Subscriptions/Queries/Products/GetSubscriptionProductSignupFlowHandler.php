<?php
namespace Ntech\Subscriptions\Queries\Products;

use Doctrine\ORM\EntityManager;
use Ntech\Subscriptions\Exceptions\SubscriptionProductDoesNotExist;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel;
use Ntech\Subscriptions\Models\SubscriptionProductSignupFlow\SubscriptionProductSignupFlowModel;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetSubscriptionProductSignupFlowHandler implements QueryHandler
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
        $this->queryBuilder = $entityManager
            ->getRepository(SubscriptionProductSignupFlowModel::class)
            ->createQueryBuilder('flow');
    }

    public function handle(GetSubscriptionProductSignupFlowQuery $query)
    {
        $this->queryBuilder->where('flow.subscriptionProduct = :subscriptionProduct')
            ->andWhere('flow.flowReference = :flowReference');
        $this->queryBuilder->setParameters([
            'subscriptionProduct' => $this->entityManager
                ->getReference(SubscriptionProductModel::class, $query->getSubscriptionProductId()->toString()),
            'flowReference' => $query->getFlowReference()
        ]);
        $result = $this->queryBuilder->getQuery()->getOneOrNullResult();

        if ($result == null) {
            throw SubscriptionProductDoesNotExist::because(
                "No signup flow model for Subscription Product ID [{$query->getSubscriptionProductId()->toString()}] 
                and reference [{$query->getFlowReference()}]"
            );
        }

        return $result;
    }
}
