<?php
namespace Ntech\Customers\Events;

use Ntech\Customers\CustomerContact;
use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class CustomerContactUpdated extends Event implements Serializable
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
        Uuid $contactId,
        CustomerContact $customerContact
    ) {
        $this->customerId = $customerId;
        $this->contactId = $contactId;
        $this->customerContact = $customerContact;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['customerId']),
            Uuid::fromString($data['contactId']),
            CustomerContact::deserialize($data['customerContact'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'contactId' => $this->contactId->toString(),
            'customerContact' => $this->customerContact->serialize()
        ];
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
