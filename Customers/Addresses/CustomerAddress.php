<?php
namespace Ntech\Customers\Addresses;

use Ntech\Customers\Models\SingleDoctrine\CustomerAddressDoctrineModel;
use Ntech\Uuid\Uuid;
use NtechUtility\PhysicalAddress\IsAPostalAddress;
use NtechUtility\PhysicalAddress\PostalAddress;
use NtechUtility\Serializer\Serializable;

class CustomerAddress implements IsAPostalAddress, Serializable
{
    /**
     * @var Uuid
     */
    private $addressId;
    /**
     * @var PostalAddress
     */
    private $address;
    /**
     * @var string
     */
    private $alias;

    public function __construct(
        Uuid $addressId,
        PostalAddress $address,
        string $alias = ""
    ) {
        $this->addressId = $addressId;
        $this->address = $address;
        $this->alias = $alias;
    }

    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['addressId']),
            new PostalAddress(
                $data['address']['nameNumber'],
                $data['address']['street1'],
                $data['address']['street2'],
                $data['address']['city'],
                $data['address']['county'],
                $data['address']['country'],
                $data['address']['postcode']
            ),
            $data['alias']
        );
    }

    public function serialize()
    {
        return [
            'addressId' => $this->addressId->toString(),
            'address' => $this->address->toArray(),
            'alias' => $this->alias
        ];
    }

    /**
     * @return Uuid
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    public function getNameNumber()
    {
        return $this->address->getNameNumber();
    }

    public function getStreet1()
    {
        return $this->address->getStreet1();
    }

    public function getStreet2()
    {
        return $this->address->getStreet2();
    }

    public function getCity()
    {
        return $this->address->getCity();
    }

    public function getCounty()
    {
        return $this->address->getCounty();
    }

    public function getCountry()
    {
        return $this->address->getCountry();
    }

    public function getPostcode()
    {
        return $this->address->getPostcode();
    }

    /**
     * @return PostalAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }


}
