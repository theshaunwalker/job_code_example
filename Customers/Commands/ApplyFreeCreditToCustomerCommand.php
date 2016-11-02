<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;

class ApplyFreeCreditToCustomerCommand extends Command
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Amount
     */
    private $amount;

    public function __construct(
        Uuid $customerId,
        Amount $amount
    ) {
        $this->customerId = $customerId;
        $this->amount = $amount;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

}
