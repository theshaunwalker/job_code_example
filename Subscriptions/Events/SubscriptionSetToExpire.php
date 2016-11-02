<?php
namespace Ntech\Subscriptions\Events;

use Ntech\Events\Event;
use Ntech\Subscriptions\Terms\SubscriptionExpiration;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionSetToExpire extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var SubscriptionExpiration
     */
    private $expiration;

    public function __construct(
        Uuid $subscriptionId,
        SubscriptionExpiration $expiration
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->expiration = $expiration;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            SubscriptionExpiration::deserialize($data['expiration'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionIt' => $this->subscriptionId,
            'expiration' => $this->expiration->serialize()
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
     * @return SubscriptionExpiration
     */
    public function getExpiration()
    {
        return $this->expiration;
    }
}
