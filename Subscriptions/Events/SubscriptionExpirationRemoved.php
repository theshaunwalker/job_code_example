<?php
namespace Ntech\Subscriptions\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionExpirationRemoved extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;

    public function __construct(
        Uuid $subscriptionId
    ) {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return self(
            Uuid::fromString($data['subscriptionId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString()
        ];
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }
}
