<?php
namespace Ntech\Subscriptions\Models\Subscription;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Ntech\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions_periods")
 */
class SubscriptionPeriodDoctrineModel
{
    /**
     * @var int
     * @ORM\Column(name="subscription_id", type="guid")
     * @ORM\Id
     */
    private $subscriptionId;
    /**
     * @var int
     * @ORM\Column(name="order_count", type="integer")
     * @ORM\Id
     */
    private $periodCount;
    /**
     * @var SubscriptionDoctrineModel
     * @ORM\ManyToOne(targetEntity="Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel", inversedBy="periods")
     */
    private $subscription;
    /**
     * @var Carbon
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;
    /**
     * @var Carbon
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;
    
    public function __construct(
        $subscription,
        int $periodCount,
        Carbon $startDate,
        Carbon $endDate
    ) {
        $this->subscription = $subscription;
        $this->subscriptionId = $subscription->getId();
        $this->periodCount = $periodCount;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return SubscriptionDoctrineModel
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return int
     */
    public function getPeriodCount()
    {
        return $this->periodCount;
    }

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return Carbon
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
