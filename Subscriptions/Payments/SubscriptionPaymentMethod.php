<?php
namespace Ntech\Subscriptions\Payments;

use Ntech\Payments\Methods\AutomatedSubscriptionMethod;
use Ntech\Uuid\Uuid;

class SubscriptionPaymentMethod
{
    /**
     * @var Uuid
     */
    private $subscriptionId;

    public function __construct(
        Uuid $subscriptionId,
        Uuid $subscriptionReference,
        string $methodKey,
        string $methodSubscriptionReference
    ) {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

}
