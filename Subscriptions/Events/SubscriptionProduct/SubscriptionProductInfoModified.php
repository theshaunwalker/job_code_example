<?php
namespace Ntech\Subscriptions\Events\SubscriptionProduct;

use Ntech\Events\Event;
use Ntech\Subscriptions\Products\SubscriptionProductInfo;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionProductInfoModified extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subProductId;
    /**
     * @var SubscriptionProductInfo
     */
    private $info;

    public function __construct(
        Uuid $subProductId,
        SubscriptionProductInfo $info
    ) {
        $this->subProductId = $subProductId;
        $this->info = $info;
    }
    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['id']),
            SubscriptionProductInfo::deserialize($data['newInfo'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'id' => $this->subProductId->toString(),
            'newInfo' => $this->info->serialize()
        ];
    }

    /**
     * @return Uuid
     */
    public function getSubProductId()
    {
        return $this->subProductId;
    }

    /**
     * @return SubscriptionProductInfo
     */
    public function getInfo()
    {
        return $this->info;
    }
}
