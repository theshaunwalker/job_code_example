<?php
namespace Ntech\Subscriptions\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionCancelled extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var string
     */
    private $reason;

    public function __construct(
        Uuid $subscriptionId,
        string $reason
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->reason = $reason;
    }

    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            $data['reason']
        );
    }

    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString(),
            'reason' => $this->reason
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
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
