<?php
namespace Ntech\Subscriptions\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Subscriptions\Subscription;
use Ntech\Support\Collections\PaginatedCollection;
use NtechUtility\Cqrs\Query\Doctrine\DoctrineQueryPagination;
use NtechUtility\Cqrs\Query\PaginatedResults;
use NtechUtility\Cqrs\Query\QueryHandler;
use NtechUtility\Support\Pagination\Pagination;

class FindDueSubscriptionsForCustomerHandler implements QueryHandler
{
    use DoctrineQueryPagination;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function handle(FindDueSubscriptionsForCustomerQuery $query)
    {
        $qb = $this->entityManager->getRepository(SubscriptionDoctrineModel::class)
            ->createQueryBuilder('s');
        $qb->join('s.paymentStatus', 'ps')
            ->where('ps.status = :paymentStatus')
            ->andWhere('s.status = :subStatus')
            ->setParameters([
                'paymentStatus' => Subscription::PAYMENT_DUE,
                'subStatus' => Subscription::STATUS_ACTIVE
            ]);
        $totalQb = clone $qb;
        $qb = $this->paginateDoctrineQuery($qb, $query->getPaginationPage(), $query->getPaginationLimit());
        $result = $qb->getQuery()
            ->getResult();

        $total = $totalQb->select('count(s)')
            ->getQuery()
            ->getSingleScalarResult();

        return new PaginatedCollection(
            $result,
            new Pagination(
                $query->getPaginationPage(),
                $query->getPaginationLimit(),
                $total
            )
        );
    }
}
