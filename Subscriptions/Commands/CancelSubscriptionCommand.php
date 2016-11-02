<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class CancelSubscriptionCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var string
     */
    private $reason;

    public function __construct(
        Uuid $subscriptionId,
        string $reason
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->reason = $reason;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
