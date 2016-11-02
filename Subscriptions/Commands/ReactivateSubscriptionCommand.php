<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class ReactivateSubscriptionCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionId;

    public function __construct(
        Uuid $subscriptionId
    ) {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }
}
