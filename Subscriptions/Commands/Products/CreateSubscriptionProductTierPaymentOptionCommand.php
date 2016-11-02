<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOption;
use Ntech\Uuid\Uuid;

class CreateSubscriptionProductTierPaymentOptionCommand extends Command
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
     * @var TierPaymentOption
     */
    private $paymentOption;

    public function __construct(
        Uuid $subscriptionProductId,
        Uuid $tierId,
        TierPaymentOption $paymentOption
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->paymentOption = $paymentOption;
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
     * @return TierPaymentOption
     */
    public function getPaymentOption()
    {
        return $this->paymentOption;
    }
}
