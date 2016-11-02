<?php
namespace Ntech\Subscriptions\Models\SubscriptionDues;

use Ntech\Database\Doctrine\DoctrineReadRepository;
use Ntech\Database\ReadOnlyRepository;
use Ntech\Uuid\Uuid;

class SubscriptionDuesRepository extends DoctrineReadRepository
{
    /**
     * Class name of read model entity
     * @return string
     */
    public function getReadModelEntityClass()
    {
        return SubscriptionDueModel::class;
    }

    public function getSubscriptionDueForPeriod(Uuid $subscriptionId, int $periodId)
    {
        return $this->where([
            'subscriptionId' => $subscriptionId->toString(),
            'periodId' => $periodId
        ])->firstOrFail();
    }
}
