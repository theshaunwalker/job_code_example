<?php
namespace Ntech\Subscriptions\Events\SubscriptionProduct;

use Ntech\Events\Event;
use Ntech\Subscriptions\Products\Tiers\TierInfo;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionProductTierInfoModified extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Uuid
     */
    private $tierId;
    /**
     * @var TierInfo
     */
    private $tierInfo;

    public function __construct(
        Uuid $subscriptionProductId,
        Uuid $tierId,
        TierInfo $tierInfo
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->tierInfo = $tierInfo;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionProductId']),
            Uuid::fromString($data['tierId']),
            TierInfo::deserialize($data['info'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionProductId' => $this->subscriptionProductId->toString(),
            'tierId' => $this->tierId->toString(),
            'info' => $this->tierInfo->serialize()
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

    /**
     * @return TierInfo
     */
    public function getTierInfo()
    {
        return $this->tierInfo;
    }
}
