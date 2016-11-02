<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Subscriptions\Products\Tiers\Tier;
use Ntech\Uuid\Uuid;

class RetireSubscriptionProductTierCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Tier
     */
    private $tierId;

    public function __construct(
        Uuid $subscriptionProductId,
        Uuid $tierId
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return Uuid
     */
    public function getTierId()
    {
        return $this->tierId;
    }
}
