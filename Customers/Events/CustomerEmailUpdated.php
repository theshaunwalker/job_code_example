<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class CustomerEmailUpdated extends Event implements Serializable
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
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['customerId']),
            $data['newEmail']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'newEmail' => $this->newEmail
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
     * @return string
     */
    public function getNewEmail()
    {
        return $this->newEmail;
    }
}
