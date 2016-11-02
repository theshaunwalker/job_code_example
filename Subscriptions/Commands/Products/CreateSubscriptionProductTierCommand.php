<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOption;
use Ntech\Subscriptions\Products\Tiers\Tier;
use Ntech\Uuid\Uuid;

class CreateSubscriptionProductTierCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Tier
     */
    private $tier;

    public function __construct(
        Uuid $subscriptionProductId,
        Tier $tier
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tier = $tier;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return Tier
     */
    public function getTier()
    {
        return $this->tier;
    }
}
