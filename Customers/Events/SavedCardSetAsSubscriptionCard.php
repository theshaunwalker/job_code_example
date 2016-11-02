<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SavedCardSetAsSubscriptionCard extends Event implements Serializable
{

    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $savedMethodId;

    public function __construct(
        Uuid $customerId,
        Uuid $savedMethodId
    ) {
        $this->customerId = $customerId;
        $this->savedMethodId = $savedMethodId;
    }
    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['customerId']),
            Uuid::fromString($data['savedMethodId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'savedMethodId' => $this->savedMethodId->toString()
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
    public function getSavedMethodId()
    {
        return $this->savedMethodId;
    }
}
