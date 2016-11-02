<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class UserAddedToCustomer extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $userId;

    public function __construct(
        Uuid $customerId,
        Uuid $userId
    ) {
        $this->customerId = $customerId;
        $this->userId = $userId;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['customerId']),
            Uuid::fromString($data['userId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'userId' => $this->userId->toString()
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
    public function getUserId()
    {
        return $this->userId;
    }
}
