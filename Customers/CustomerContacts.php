<?php
namespace Ntech\Customers;

use Ntech\Customers\Events\ContactAddedToCustomer;
use Ntech\Customers\Events\ContactRemovedFromCustomer;
use Ntech\Customers\Events\ContactSetAsPrimary;
use Ntech\Customers\Events\CustomerContactUpdated;
use NtechUtility\EventSource\EventSourcedEntity;
use NtechUtility\EventSource\EventSourcedEntityTrait;
use NtechUtility\Support\Collections\Collection;

class CustomerContacts implements EventSourcedEntity
{
    use EventSourcedEntityTrait;

    private $contacts;

    private $primary;

    public function __construct()
    {
        $this->contacts = new Collection;
    }

    public function applyContactAddedToCustomer(ContactAddedToCustomer $event)
    {
        $this->contacts->put($event->getCustomerContact()->getId(), $event->getCustomerContact());
    }

    public function applyContactSetAsPrimary(ContactSetAsPrimary $event)
    {
        $this->primary = $this->contacts->get($event->getContactId());
    }
    
    public function applyCustomerContactUpdated(CustomerContactUpdated $event)
    {
        $this->contacts->put($event->getContactId(), $event->getCustomerContact());
    }
    
    public function applyContactRemovedFromCustomer(ContactRemovedFromCustomer $event)
    {
        $this->contacts->forget($event->getContactId()->toString());
    }
}
