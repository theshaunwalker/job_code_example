<?php
namespace Ntech\Subscriptions\Periods;

use Carbon\Carbon;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionPeriod implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * The position of this period in relation to other periods
     * @var int
     */
    private $orderCount;
    /**
     * @var Carbon
     */
    private $startDate;
    /**
     * @var Carbon
     */
    private $endDate;

    public function __construct(
        Uuid $subscriptionId,
        int $orderCount,
        Carbon $startDate,
        Carbon $endDate
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->orderCount = $orderCount;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            $data['orderCount'],
            new Carbon($data['startDate']),
            new Carbon($data['endDate'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString(),
            'orderCount' => $this->orderCount,
            'startDate' => $this->startDate->toDateTimeString(),
            'endDate' => $this->endDate->toDateTimeString()
        ];
    }

    public static function fromSubscriptionTerms(
        Uuid $subscriptionId,
        int $orderCount,
        Carbon $startDate,
        SubscriptionTerms $terms
    ) {
        $endDate = $terms->calculateEndDate(
            $startDate
        );

        return new self(
            $subscriptionId,
            $orderCount,
            $startDate,
            $endDate
        );
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @return int
     */
    public function getOrderCount()
    {
        return $this->orderCount;
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

    /**
     * Generate a new period which follows this one according to passed terms
     * @param SubscriptionTerms $terms
     * @return SubscriptionPeriod
     */
    public function nextPeriod(SubscriptionTerms $terms): SubscriptionPeriod
    {
        return new self(
            $this->subscriptionId,
            $this->orderCount + 1,
            $this->getEndDate(),
            $terms->calculateEndDate($this->getEndDate())
        );
    }

    /**
     * Does this subscription period contain this date?
     * @param Carbon $date
     */
    public function containsDate(Carbon $date)
    {
        return $this->getStartDate() <= $date && $date <= $this->getEndDate();
    }
}
