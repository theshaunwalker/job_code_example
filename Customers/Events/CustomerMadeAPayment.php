<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;
use NtechUtility\Serializer\Serializable;

class CustomerMadeAPayment extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Amount
     */
    private $amount;
    /**
     * One of the SOURCE constants from Ntech\Customers\Payments\CustomerPayment
     * @var int
     */
    private $source;

    public function __construct(
        Uuid $customerId,
        Amount $amount,
        int $source
    ) {
        $this->customerId = $customerId;
        $this->amount = $amount;
        $this->source = $source;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        // TODO: Implement deserialize() method.
    }

    /**
     * @return array
     */
    public function serialize()
    {
        // TODO: Implement serialize() method.
    }
}
