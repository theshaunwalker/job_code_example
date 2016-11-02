<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Ntech\Customers\CustomerContact;
use Ntech\Uuid\Uuid;

class UpdateCustomerContactCommand extends Command
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $contactId;
    /**
     * @var CustomerContact
     */
    private $updatedContact;

    public function __construct(
        Uuid $customerId,
        Uuid $contactId,
        CustomerContact $updatedContact
    ) {
        $this->customerId = $customerId;
        $this->contactId = $contactId;
        $this->updatedContact = $updatedContact;
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

    /**
     * @return CustomerContact
     */
    public function getUpdatedContact()
    {
        return $this->updatedContact;
    }
}
