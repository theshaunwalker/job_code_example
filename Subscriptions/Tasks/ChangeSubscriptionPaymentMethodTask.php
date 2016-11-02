<?php
namespace Ntech\Subscriptions\Tasks;

use Ntech\Payments\Methods\IsASubscribeableMethod;
use Ntech\Uuid\Uuid;
use NtechUtility\Tasks\Task;

class ChangeSubscriptionPaymentMethodTask implements Task
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var IsASubscribeableMethod
     */
    private $newSubscribeableMethod;

    public function __construct(
        Uuid $subscriptionId,
        IsASubscribeableMethod $newSubscribeableMethod
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->newSubscribeableMethod = $newSubscribeableMethod;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @return IsASubscribeableMethod
     */
    public function getNewSubscribeableMethod()
    {
        return $this->newSubscribeableMethod;
    }
}
