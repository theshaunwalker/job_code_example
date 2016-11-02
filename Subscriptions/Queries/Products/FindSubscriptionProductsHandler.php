<?php
namespace Ntech\Subscriptions\Queries\Products;

use Doctrine\ORM\EntityManager;
use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel;
use NtechUtility\Cqrs\Query\QueryHandler;
use NtechUtility\Support\Collections\Collection;

class FindSubscriptionProductsHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $subscriptionProductsRepository;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionProductsRepository = $entityManager->getRepository(SubscriptionProductModel::class);
    }
    public function handle(FindSubscriptionProductsQuery $query)
    {
        $qb = $this->subscriptionProductsRepository->createQueryBuilder('sp');
        $qb->where('sp.company = :company');
        $qb->setParameters([
            'company' => $this->entityManager->getReference(CompanyDoctrineModel::class, $query->getCompanyId()->toString())
        ]);
        $docQuery = $qb->getQuery();
        $results = $docQuery->getResult();
        return new Collection($results);
    }
}
