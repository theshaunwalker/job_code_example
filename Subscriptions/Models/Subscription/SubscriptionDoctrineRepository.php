<?php
namespace Ntech\Subscriptions\Models\Subscription;

use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Database\Doctrine\DoctrineReadRepository;
use Ntech\Subscriptions\Subscription;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\PaginateCriteria;
use NtechUtility\Support\Collections\Collection;
use NtechUtility\Support\Collections\PaginatedCollection;

class SubscriptionDoctrineRepository extends DoctrineReadRepository
{

    /**
     * Class name of read model entity
     * @return string
     */
    public function getReadModelEntityClass()
    {
        return SubscriptionDoctrineModel::class;
    }

    protected function whereCustomerId(Uuid $customerId)
    {
        return $this->where([
            'customer' => $this->entityManager->getReference(CustomerDoctrineModel::class, $customerId->toString())
        ]);
    }

    public function getAllForCustomer(Uuid $customerId)
    {
        $results = $this->whereCustomerId($customerId)->retrieve();
        return new Collection($results);
    }

    public function getForCustomer(Uuid $customerId, PaginateCriteria $paginateCriteria): PaginatedCollection
    {
        $results = $this->whereCustomerId($customerId)->withPaginateCriteria($paginateCriteria)
            ->getPaginatedResults();
        return $results;
    }

    public function getActiveSubscriptionCountForCustomer(Uuid $customerId)
    {
        $status = Subscription::STATUS_ACTIVE;
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select($qb->expr()->count('s'));
        $qb->from($this->getReadModelEntityClass(), 's');
        $qb->where("s.customer = '{$customerId->toString()}'
            and s.status = {$status}");
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getSuspendedSubscriptionCountForCustomer(Uuid $customerId)
    {

        $status = Subscription::STATUS_SUSPENDED;
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select($qb->expr()->count('s'));
        $qb->from($this->getReadModelEntityClass(), 's');
        $qb->where("s.customer = '{$customerId->toString()}'
            and s.status = {$status}");
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getCancelledSubscriptionCountForCustomer(Uuid $customerId)
    {

        $status = Subscription::STATUS_CANCELLED;
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select($qb->expr()->count('s'));
        $qb->from($this->getReadModelEntityClass(), 's');
        $qb->where("s.customer = '{$customerId->toString()}'
            and s.status = {$status}");
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }
}
