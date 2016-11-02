<?php
namespace Ntech\Subscriptions\Events\SubscriptionProduct;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionProductTierRetired extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Uuid
     */
    private $tierId;

    public function __construct(
        Uuid $subscriptionProductId,
        Uuid $tierId
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionProductId']),
            Uuid::fromString($data['tierId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionProductId' => $this->subscriptionProductId->toString(),
            'tierId' => $this->tierId
        ];
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return Uuid
     */
    public function getTierId()
    {
        return $this->tierId;
    }
}
