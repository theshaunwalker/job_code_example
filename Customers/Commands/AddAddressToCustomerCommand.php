<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Ntech\Customers\Addresses\CustomerAddress;
use Ntech\Uuid\Uuid;

class AddAddressToCustomerCommand extends Command
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var CustomerAddress
     */
    private $address;
    /**
     * @var array Array of integers flagging address as default for shipping/billing etc.
     */
    private $addressDefaultsFor;

    public function __construct(
        Uuid $customerId,
        CustomerAddress $address,
        array $addressDefaultsFor
    ) {
        $this->customerId = $customerId;
        $this->address = $address;
        $this->addressDefaultsFor = $addressDefaultsFor;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return CustomerAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return array
     */
    public function getAddressDefaultsFor()
    {
        return $this->addressDefaultsFor;
    }

}
