<?php
namespace Ntech\Subscriptions\Products\SignupFlow;

use Ntech\Subscriptions\Events\SubscriptionProductSignupFlow\SubscriptionProductFlowCreated;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use Ntech\Uuid\Uuid;
use NtechUtility\EventSource\EventSourcedAggregateRoot;
use NtechUtility\EventSource\EventSourcedAggregateRootTrait;

class SubscriptionProductSignupFlow implements EventSourcedAggregateRoot
{
    use EventSourcedAggregateRootTrait;

    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var string
     */
    private $flowReference;
    /**
     * @var SignupSettingss
     */
    private $signupSettings;

    /**
     * @return Uuid
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }

    public static function newFlow(
        Uuid $flowId,
        Uuid $subscriptionProductId,
        string $flowReference,
        SignupSettings $signupSettings
    ) {
        $signupFlow = new self();
        $signupFlow->apply(
            new SubscriptionProductFlowCreated(
                $flowId,
                $subscriptionProductId,
                $flowReference,
                $signupSettings
            )
        );
        return $signupFlow;
    }

    public function applySubscriptionProductFlowCreated(SubscriptionProductFlowCreated $event)
    {
        $this->id = $event->getFlowId();
        $this->subscriptionProductId = $event->getSubscriptionProductId();
        $this->flowReference = $event->getFlowReference();
        $this->signupSettings = $event->getSignupSettings();
    }
}
