<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class UpdateCustomersEmailCommand extends Command
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var string
     */
    private $newEmail;

    public function __construct(
        Uuid $customerId,
        string $newEmail
    ) {
        $this->customerId = $customerId;
        $this->newEmail = $newEmail;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getNewEmail()
    {
        return $this->newEmail;
    }
}
