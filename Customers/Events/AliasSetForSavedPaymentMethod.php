<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class AliasSetForSavedPaymentMethod extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $savedMethodId;
    /**
     * @var string
     */
    private $alias;

    public function __construct(
        Uuid $customerId,
        Uuid $savedMethodId,
        string $alias
    ) {
        $this->customerId = $customerId;
        $this->savedMethodId = $savedMethodId;
        $this->alias = $alias;
    }
    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['customerId']),
            Uuid::fromString($data['savedMethodId']),
            $data['alias']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'savedMethodId' => $this->savedMethodId->toString(),
            'alias' => $this->alias
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

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
