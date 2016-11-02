<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class SetAddressAsBillingCommand extends Command
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
