<?php
namespace Ntech\Subscriptions\Events;

use Ntech\Events\Event;
use Ntech\Payments\Methods\PaymentMethodSubscription;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class PaymentMethodAttachedToSubscription extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var Uuid
     */
    private $paymentSubscriptionId;

    public function __construct(
        Uuid $subscriptionId,
        Uuid $paymentSubscriptionId
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->paymentSubscriptionId = $paymentSubscriptionId;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            Uuid::fromString($data['paymentSubscriptionId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString(),
            'paymentSubscriptionId' => $this->paymentSubscriptionId->toString()
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
    public function getPaymentSubscriptionId()
    {
        return $this->paymentSubscriptionId;
    }

}
