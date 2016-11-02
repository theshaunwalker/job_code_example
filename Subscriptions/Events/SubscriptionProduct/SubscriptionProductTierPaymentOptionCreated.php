<?php
namespace Ntech\Subscriptions\Events\SubscriptionProduct;

use Ntech\Events\Event;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOption;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionProductTierPaymentOptionCreated extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Uuid
     */
    private $tierId;
    /**
     * @var TierPaymentOption
     */
    private $paymentOption;

    public function __construct(
        Uuid $companyId,
        Uuid $subscriptionProductId,
        Uuid $tierId,
        TierPaymentOption $paymentOption
    ) {
        $this->companyId = $companyId;
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->paymentOption = $paymentOption;
    }


    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['companyId']),
            Uuid::fromString($data['subscriptionProductId']),
            Uuid::fromString($data['tierId']),
            TierPaymentOption::deserialize($data['paymentOption'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'companyId' => $this->companyId->toString(),
            'subscriptionProductId' => $this->subscriptionProductId->toString(),
            'tierId' => $this->tierId->toString(),
            'paymentOption' => $this->paymentOption->serialize()
        ];
    }

    /**
     * @return Uuid
     */
    public function getCompanyId()
    {
        return $this->companyId;
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
     * @return TierPaymentOption
     */
    public function getPaymentOption()
    {
        return $this->paymentOption;
    }
}
