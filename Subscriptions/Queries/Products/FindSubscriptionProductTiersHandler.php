<?php
namespace Ntech\Subscriptions\Queries\Products;

use Doctrine\ORM\EntityManager;
use Ntech\Exceptions\Query\NoResultsFound;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierModel;
use NtechUtility\Cqrs\Query\QueryHandler;
use NtechUtility\Support\Collections\Collection;

class FindSubscriptionProductTiersHandler implements QueryHandler
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
            ->getRepository(SubscriptionProductTierModel::class)->createQueryBuilder('tier');
    }
    
    public function handle(FindSubscriptionProductTiersQuery $query)
    {
        $this->queryBuilder->where('tier.subscriptionProduct = :subscriptionProduct');
        $this->queryBuilder->setParameters([
            'subscriptionProduct' => $this->entityManager->getReference(SubscriptionProductTierModel::class, $query->getSubscriptionProductId())
        ]);

        $results = $this->queryBuilder->getQuery()->getResult();
        if (empty($results)) {
            return NoResultsFound::forQuery($query);
        }
        return new Collection($results);
    }
}
