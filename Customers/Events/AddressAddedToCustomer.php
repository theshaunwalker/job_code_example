<?php
namespace Ntech\Customers\Events;

use Ntech\Customers\Addresses\CustomerAddress;
use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class AddressAddedToCustomer extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $customerId;

    /**
     * @var Uuid
     */
    private $addressId;
    /**
     * @var CustomerAddress
     */
    private $address;

    public function __construct(
        Uuid $customerId,
        Uuid $addressId,
        CustomerAddress $address
    ) {
        $this->customerId = $customerId;
        $this->addressId = $addressId;
        $this->address = $address;
    }

    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['customerId']),
            Uuid::fromString($data['addressId']),
            CustomerAddress::deserialize($data['address'])
        );
    }

    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'addressId' => $this->addressId->toString(),
            'address' => $this->address->serialize()
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

    /**
     * @return CustomerAddress
     */
    public function getAddress()
    {
        return $this->address;
    }
}
