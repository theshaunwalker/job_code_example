<?php
namespace Ntech\Subscriptions\Products\Tiers;

use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class Tier implements Serializable
{
    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var TierInfo
     */
    private $info;
    /**
     * @var bool
     */
    private $retired;

    public function __construct(
        Uuid $id,
        Uuid $subscriptionProductId,
        TierInfo $info,
        bool $retired = false
    ) {
        $this->id = $id;
        $this->subscriptionProductId = $subscriptionProductId;
        $this->info = $info;
        $this->retired = $retired;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['id']),
            Uuid::fromString($data['subscriptionProductId']),
            TierInfo::deserialize($data['info']),
            $data['retired']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'id' => $this->id->toString(),
            'subscriptionProductId' => $this->subscriptionProductId->toString(),
            'info' => $this->info->serialize(),
            'retired' => $this->retired
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
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return TierInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return boolean
     */
    public function isRetired()
    {
        return $this->retired;
    }
    public function getName()
    {
        
    }
}
