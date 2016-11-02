<?php
namespace Ntech\Customers;

use Ntech\Customers\Addresses\CustomerAddress;
use Ntech\Customers\Events\AddressAddedToCustomer;
use Ntech\Customers\Events\AddressRemovedFromCustomer;
use Ntech\Customers\Events\CustomerSetBillingAddress;
use Ntech\Customers\Events\CustomerSetPrimaryAddress;
use Ntech\Customers\Events\CustomerSetShippingAddress;
use Ntech\Uuid\Uuid;
use NtechUtility\EventSource\EventSourcedEntity;
use NtechUtility\EventSource\EventSourcedEntityTrait;
use NtechUtility\Support\Collections\Collection;

class CustomerAddresses implements EventSourcedEntity
{
    use EventSourcedEntityTrait;

    /**
     * @var Collection
     */
    private $addresses;

    private $primary;

    private $shipping;

    private $billing;

    const DEFAULT_ADDRESS_FOR_EVERYTHING = 0;
    const PRIMARY_ADDRESS = 1;
    const BILLING_ADDRESS = 2;
    const SHIPPING_ADDRESS = 3;

    public function __construct()
    {
        $this->addresses = new Collection();
    }

    public function applyAddressAddedToCustomer(AddressAddedToCustomer $event)
    {
        $addressId = $event->getAddressId();
        $this->addresses->put($addressId->toString(), $event->getAddress());
    }

    public function applyAddressRemovedFromCustomer(AddressRemovedFromCustomer $event)
    {
        $this->addresses->forget($event->getAddressId()->toString());
    }

    public function applyCustomerSetPrimaryAddress(CustomerSetPrimaryAddress $event)
    {
        $this->primary = $this->addresses->get($event->getAddressId()->toString());
    }

    public function applyCustomerSetBillingAddress(CustomerSetBillingAddress $event)
    {
        $this->billing = $this->addresses->get($event->getAddressId()->toString());
    }

    public function applyCustomerSetShippingAddress(CustomerSetShippingAddress $event)
    {
        $this->shipping = $this->addresses->get($event->getAddressId()->toString());
    }

}
