<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use NtechUtility\Serializer\Serializable;
use Ntech\Uuid\Uuid;

class ContactSetAsPrimary extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $contactId;

    public function __construct(Uuid $customerId, Uuid $contactId)
    {
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

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['customerId']),
            Uuid::fromString($data['contactId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'contactId' => $this->contactId->toString()
        ];
    }
}
