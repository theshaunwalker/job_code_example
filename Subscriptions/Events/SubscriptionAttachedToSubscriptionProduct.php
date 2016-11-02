<?php
namespace Ntech\Subscriptions\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionAttachedToSubscriptionProduct extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Uuid
     */
    private $tierId;
    /**
     * @var Uuid
     */
    private $tierPaymentOptionId;

    public function __construct(
        Uuid $subscriptionId,
        Uuid $subscriptionProductId,
        Uuid $tierId,
        Uuid $tierPaymentOptionId
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->tierPaymentOptionId = $tierPaymentOptionId;
    }
    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            Uuid::fromString($data['subscriptionProductId']),
            Uuid::fromString($data['tierId']),
            Uuid::fromString($data['tierPaymentOptionId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString(),
            'subscriptionProductId' => $this->subscriptionProductId->toString(),
            'tierId' => $this->tierId->toString(),
            'tierPaymentOptionId' => $this->tierPaymentOptionId->toString()
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
     * @return Uuid
     */
    public function getTierPaymentOptionId()
    {
        return $this->tierPaymentOptionId;
    }
}
