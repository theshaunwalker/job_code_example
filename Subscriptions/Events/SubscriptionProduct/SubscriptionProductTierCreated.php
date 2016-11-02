<?php
namespace Ntech\Subscriptions\Events\SubscriptionProduct;

use Ntech\Events\Event;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOption;
use Ntech\Subscriptions\Products\Tiers\Tier;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionProductTierCreated extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Tier
     */
    private $tier;

    public function __construct(
        Uuid $subscriptionProductId,
        Tier $tier
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tier = $tier;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionProductId']),
            Tier::deserialize($data['tier'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionProductId' => $this->subscriptionProductId->toString(),
            'tier' => $this->tier->serialize()
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
     * @return Tier
     */
    public function getTier()
    {
        return $this->tier;
    }
}
