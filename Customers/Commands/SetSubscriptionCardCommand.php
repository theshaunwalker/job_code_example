<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class SetSubscriptionCardCommand extends Command
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $methodId;

    public function __construct(
        Uuid $customerId,
        Uuid $methodId
    ) {
        $this->customerId = $customerId;
        $this->methodId = $methodId;
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
    public function getMethodId()
    {
        return $this->methodId;
    }
}
