<?php
namespace Ntech\Subscriptions;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Ntech\Exceptions\DomainException;
use Ntech\Subscriptions\Terms\SubscriptionExpiration;
use NtechUtility\Money\Amount;
use NtechUtility\Serializer\Serializable;

class SubscriptionTerms implements Serializable
{
    /**
     * Available intervals.
     * @var array
     */
    public static $intervals = [
        self::DAY_INTERVAL,
        self::WEEK_INTERVAL,
        self::MONTH_INTERVAL
    ];

    /**
     * @var Amount
     */
    private $rate;

    const DAY_INTERVAL = 'day';
    const WEEK_INTERVAL = 'week';
    const MONTH_INTERVAL = 'month';
    /**
     * @var string
     */
    private $interval;

    /**
     * @var int
     */
    private $intervalCount;

    /**
     * Is this a prepay subscription?
     * true = subscription must be charged to progress to next period
     * false = charges are calculated at end of subscription period so
     *      can automatically transition periods
     * @var bool
     */
    private $prepay;
    
    public function __construct(
        Amount $rate,
        string $interval,
        int $intervalCount,
        bool $prepay = true
    ) {
        $this->rate = $rate;
        // Interval type must be one of the allowed values in constant self::$intervals
        if (!in_array($interval, self::$intervals)) {
            throw DomainException::because(
                "Provided subscription interval [{$interval}] is invalid. Must be one of " .
                implode(", ", self::$intervals)
            );
        }
        // Interval count must be positive integer
        if ($intervalCount <= 0) {
            throw DomainException::because(
                "Provided subscription interval count must be positive, got [{$intervalCount}]"
            );
        }
        $this->interval = $interval;
        $this->intervalCount = $intervalCount;
        $this->prepay = $prepay;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            new Amount($data['amount'], $data['amount_currency']),
            $data['interval'],
            $data['interval_count'],
            $data['prepay']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'amount' => $this->rate->getAmount(),
            'amount_currency' => $this->rate->getCurrencyCode(),
            'interval' => $this->interval,
            'interval_count' => $this->intervalCount,
            'prepay' => $this->prepay
        ];
    }

    /**
     * @return Amount
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return string
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @return int
     */
    public function getIntervalCount()
    {
        return $this->intervalCount;
    }

    /**
     * @return boolean
     */
    public function isPrepay()
    {
        return $this->prepay;
    }

    /**
     * Apply the terms to a passed start date and calculate an end date.
     * @param Carbon $startDate
     * @param SubscriptionTerms $terms
     * @return static
     */
    public function calculateEndDate(
        Carbon $startDate
    ) {
        $endDate = $startDate->copy();

        switch ($this->getInterval()) {
            case SubscriptionTerms::DAY_INTERVAL:
                $endDate->addDays($this->getIntervalCount());
                break;
            case SubscriptionTerms::WEEK_INTERVAL:
                $endDate->addWeeks($this->getIntervalCount());
                break;
            case SubscriptionTerms::MONTH_INTERVAL:
                $endDate->addMonths($this->getIntervalCount());
                break;
        }
        return $endDate;
    }
    
    public function getReadableInterval()
    {
        return Str::plural(Str::ucfirst($this->getInterval()), $this->getIntervalCount());
    }
    
    public function getReadableTerms()
    {
        return "Charge " . $this->rate->readable() . ' every ' . $this->intervalCount . ' ' . $this->getReadableInterval();
    }


}
