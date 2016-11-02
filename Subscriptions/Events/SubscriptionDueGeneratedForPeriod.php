<?php
namespace Ntech\Subscriptions\Events;

use Ntech\Events\Event;
use Ntech\Subscriptions\Dues\SubscriptionDue;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionDueGeneratedForPeriod extends Event implements Serializable
{

    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var int
     */
    private $periodId;
    /**
     * @var SubscriptionDue
     */
    private $due;

    public function __construct(
        Uuid $subscriptionId,
        int $periodId,
        SubscriptionDue $due
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->periodId = $periodId;
        $this->due = $due;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            $data['periodId'],
            SubscriptionDue::deserialize($data['due'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString(),
            'periodId' => $this->periodId,
            'due' => $this->due->serialize()
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
     * @return int
     */
    public function getPeriodId()
    {
        return $this->periodId;
    }

    /**
     * @return SubscriptionDue
     */
    public function getDue()
    {
        return $this->due;
    }

}
