<?php
namespace Ntech\Subscriptions\Events\SubscriptionProduct;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionProductTierPaymentOptionDeleted extends Event implements Serializable
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
     * @var Uuid
     */
    private $paymentOptionId;

    public function __construct(
        Uuid $companyId,
        Uuid $subscriptionProductId,
        Uuid $tierId,
        Uuid $paymentOptionId
    ) {
        $this->companyId = $companyId;
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->paymentOptionId = $paymentOptionId;
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
            Uuid::fromString($data['paymentOptionId'])
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
            'paymentOptionId' => $this->paymentOptionId->toString()
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
     * @return Uuid
     */
    public function getPaymentOptionId()
    {
        return $this->paymentOptionId;
    }
}
