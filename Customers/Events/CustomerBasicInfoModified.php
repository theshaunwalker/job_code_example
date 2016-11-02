<?php
namespace Ntech\Customers\Events;

use Ntech\Customers\CustomerBasicInfo;
use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class CustomerBasicInfoModified extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var CustomerBasicInfo
     */
    private $customerBasicInfo;

    public function __construct(
        Uuid $customerId,
        CustomerBasicInfo $customerBasicInfo
    ) {
        $this->customerId = $customerId;
        $this->customerBasicInfo = $customerBasicInfo;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['customerId']),
            CustomerBasicInfo::deserialize($data['customerBasicInfo'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'customerBasicInfo' => $this->customerBasicInfo->serialize()
        ];
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return CustomerBasicInfo
     */
    public function getCustomerBasicInfo()
    {
        return $this->customerBasicInfo;
    }
}
