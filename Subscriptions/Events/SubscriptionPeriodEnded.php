<?php
namespace Ntech\Subscriptions\Events;

use Ntech\Events\Event;
use Ntech\Subscriptions\Periods\SubscriptionPeriod;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionPeriodEnded extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var SubscriptionPeriod
     */
    private $period;

    public function __construct(
        Uuid $subscriptionId,
        SubscriptionPeriod $period
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->period = $period;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            SubscriptionPeriod::deserialize($data['period'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString(),
            'period' => $this->period->serialize()
        ];
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @return SubscriptionPeriod
     */
    public function getPeriod()
    {
        return $this->period;
    }
}
