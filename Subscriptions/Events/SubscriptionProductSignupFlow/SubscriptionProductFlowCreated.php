<?php
namespace Ntech\Subscriptions\Events\SubscriptionProductSignupFlow;

use Ntech\Events\Event;
use Ntech\Subscriptions\Products\SignupFlow\SignupSettings;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionProductFlowCreated extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $flowId;
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var string
     */
    private $flowReference;
    /**
     * @var SignupSettings
     */
    private $signupSettings;

    public function __construct(
        Uuid $flowId,
        Uuid $subscriptionProductId,
        string $flowReference,
        SignupSettings $signupSettings
    ) {
        $this->flowId = $flowId;
        $this->subscriptionProductId = $subscriptionProductId;
        $this->flowReference = $flowReference;
        $this->signupSettings = $signupSettings;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['flowId']),
            Uuid::fromString($data['subscriptionProductId']),
            $data['flowReference'],
            SignupSettings::deserialize($data['signupFlow'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'flowId' => $this->flowId->toString(),
            'subscriptionProductId' => $this->subscriptionProductId->toString(),
            'flowReference' => $this->flowReference,
            'signupFlow' => $this->signupSettings->serialize()
        ];
    }

    /**
     * @return Uuid
     */
    public function getFlowId()
    {
        return $this->flowId;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return string
     */
    public function getFlowReference()
    {
        return $this->flowReference;
    }

    /**
     * @return SignupSettings
     */
    public function getSignupSettings()
    {
        return $this->signupSettings;
    }
}
