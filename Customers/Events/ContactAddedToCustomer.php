<?php
namespace Ntech\Customers\Events;

use Ntech\Customers\CustomerContact;
use Ntech\Events\Event;
use NtechUtility\Serializer\Serializable;
use Ntech\Uuid\Uuid;

class ContactAddedToCustomer extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var CustomerContact
     */
    private $customerContact;

    /**
     * ContactAddedToCustomer constructor.
     * @param Uuid $customerId
     * @param Uuid $contactId
     * @param CustomerContact $customerContact
     */
    public function __construct(
        Uuid $customerId,
        CustomerContact $customerContact
    ) {

        $this->customerId = $customerId;
        $this->customerContact = $customerContact;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['customerId']),
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
     * @return CustomerContact
     */
    public function getCustomerContact()
    {
        return $this->customerContact;
    }

}
