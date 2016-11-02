<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Subscriptions\Products\Tiers\TierInfo;
use Ntech\Uuid\Uuid;

class UpdateSubscriptionProductTierInfoCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Uuid
     */
    private $tierId;
    /**
     * @var TierInfo
     */
    private $tierInfo;

    public function __construct(
        Uuid $subscriptionProductId,
        Uuid $tierId,
        TierInfo $tierInfo
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->tierInfo = $tierInfo;
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

    /**
     * @return TierInfo
     */
    public function getTierInfo()
    {
        return $this->tierInfo;
    }
}
