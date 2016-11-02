<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class SetContactAsPrimaryCommand extends Command
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $contactId;

    public function __construct(
        Uuid $customerId,
        Uuid $contactId
    ) {
        $this->customerId = $customerId;
        $this->contactId = $contactId;
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
    public function getContactId()
    {
        return $this->contactId;
    }
}
