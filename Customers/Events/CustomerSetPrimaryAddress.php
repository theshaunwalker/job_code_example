<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class CustomerSetPrimaryAddress extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $addressId;

    public function __construct(
        Uuid $customerId,
        Uuid $addressId
    ) {
        $this->customerId = $customerId;
        $this->addressId = $addressId;
    }

    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['customerId']),
            Uuid::fromString($data['addressId'])
        );
    }

    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'addressId' => $this->addressId->toString()
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
     * @return Uuid
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

}
