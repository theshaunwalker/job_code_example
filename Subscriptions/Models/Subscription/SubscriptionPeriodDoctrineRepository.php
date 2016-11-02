<?php
namespace Ntech\Subscriptions\Models\Subscription;

use Ntech\Database\Doctrine\DoctrineReadRepository;

class SubscriptionPeriodDoctrineRepository extends DoctrineReadRepository
{

    /**
     * Class name of read model entity
     * @return string
     */
    public function getReadModelEntityClass()
    {
        return SubscriptionPeriodDoctrineModel::class;
    }


    public function getPeriod(Uuid $subscriptionId, int $periodCount)
    {
        return $this->where([
            'subscriptionId' => $subscriptionId->toString(),
            'periodCount' => $periodCount
        ])->firstOrFail();
    }
}
