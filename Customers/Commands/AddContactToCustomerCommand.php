<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Carbon\Carbon;
use Ntech\Customers\Customer;
use Ntech\Customers\CustomerContact;
use Ntech\Events\Event;
use Ntech\Uuid\Uuid;

class AddContactToCustomerCommand extends Command
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
    private $customerContact;

    public function __construct(
        Uuid $customerId,
        CustomerContact $customerContact
    ) {
        $this->customerId = $customerId;
        $this->customerContact = $customerContact;
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
    public function getCustomerContact()
    {
        return $this->customerContact;
    }
}
