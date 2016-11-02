<?php
namespace Ntech\Customers\Models\CustomerAddresses;

use Ntech\Customers\Models\SingleDoctrine\CustomerAddressDoctrineModel;
use Ntech\Support\ValueObjects\ValueObject;
use Ntech\Customers\Addresses\CustomerAddress as DomainCustomerAddress;
use Ntech\Uuid\Uuid;
use NtechUtility\PhysicalAddress\PostalAddress;

class CustomerAddress extends ValueObject
{
    /**
     * @var DomainCustomerAddress
     */
    protected $customerAddress;

    /**
     * If the address is the primary one for the customer
     * @var bool
     */
    protected $primary;
    /**
     * If this is the billing address for the customer
     * @var bool
     */
    protected $billing;
    /**
     * If this is the shipping address for the customer
     * @var bool
     */
    protected $shipping;

    public function __construct(
        DomainCustomerAddress $customerAddress,
        bool $primary,
        bool $billing,
        bool $shipping
    ) {
        $this->customerAddress = $customerAddress;
        $this->primary = $primary;
        $this->billing = $billing;
        $this->shipping = $shipping;
    }
    
    public static function fromDoctrineModel(CustomerAddressDoctrineModel $doctrineAddress)
    {
        return new static(
            new DomainCustomerAddress(
                Uuid::fromString($doctrineAddress->getId()),
                new PostalAddress(
                    $doctrineAddress->getNameNumber(),
                    $doctrineAddress->getStreet1(),
                    $doctrineAddress->getStreet2(),
                    $doctrineAddress->getCity(),
                    $doctrineAddress->getCounty(),
                    $doctrineAddress->getCountry(),
                    $doctrineAddress->getPostcode()
                ),
                $doctrineAddress->getAlias()
            ),
            $doctrineAddress->isPrimary(),
            $doctrineAddress->isDefaultBilling(),
            $doctrineAddress->isDefaultShipping()
        );
    }

    public function getAddressId()
    {
        return $this->customerAddress->getAddressId();
    }

    public function getAlias()
    {
        return $this->customerAddress->getAlias();
    }
    
    public function getPostalAddress()
    {
        return $this->customerAddress->getAddress();
    }
}
