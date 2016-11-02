<?php
namespace Ntech\Subscriptions\Events\SubscriptionProduct;

use Ntech\Events\Event;
use Ntech\Subscriptions\Products\SubscriptionProductInfo;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionProductCreated extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var SubscriptionProductInfo
     */
    private $info;

    public function __construct(
        Uuid $subscriptionProductId,
        Uuid $companyId,
        SubscriptionProductInfo $info
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->companyId = $companyId;
        $this->info = $info;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['id']),
            Uuid::fromString($data['companyId']),
            SubscriptionProductInfo::deserialize($data['info'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'id' => $this->subscriptionProductId->toString(),
            'companyId' => $this->companyId->toString(),
            'info' => $this->info->serialize()
        ];
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return Uuid
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return SubscriptionProductInfo
     */
    public function getInfo()
    {
        return $this->info;
    }
}
