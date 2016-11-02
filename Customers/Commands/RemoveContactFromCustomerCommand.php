<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class RemoveContactFromCustomerCommand extends Command
{
    /**
     * @var Uuid
     */
    private $customerid;
    /**
     * @var Uuid
     */
    private $contactId;

    public function __construct(
        Uuid $customerid,
        Uuid $contactId
    ) {
        $this->customerid = $customerid;
        $this->contactId = $contactId;
    }

    /**
     * @return Uuid
     */
    public function getCustomerid()
    {
        return $this->customerid;
    }

    /**
     * @return Uuid
     */
    public function getContactId()
    {
        return $this->contactId;
    }
}
