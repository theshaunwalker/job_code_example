<?php
namespace Ntech\Subscriptions\Products\Tiers\PaymentOptions;

use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;
use NtechUtility\Serializer\Serializable;

class TierPaymentOption implements Serializable
{
    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var TierPaymentOptionInfo
     */
    private $info;
    /**
     * @var SubscriptionTerms
     */
    private $terms;

    public function __construct(
        Uuid $id,
        TierPaymentOptionInfo $info,
        SubscriptionTerms $terms
    ) {
        $this->id = $id;
        $this->info = $info;
        $this->terms = $terms;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['id']),
            TierPaymentOptionInfo::deserialize($data['info']),
            SubscriptionTerms::deserialize($data['terms'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'id' => $this->id->toString(),
            'info' => $this->info->serialize(),
            'terms' => $this->terms->serialize()
        ];
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TierPaymentOptionInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return SubscriptionTerms
     */
    public function getTerms()
    {
        return $this->terms;
    }
}
