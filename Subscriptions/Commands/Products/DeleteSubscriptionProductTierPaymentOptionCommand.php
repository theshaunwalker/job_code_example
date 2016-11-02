<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class DeleteSubscriptionProductTierPaymentOptionCommand extends Command
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
     * @var Uuid
     */
    private $paymentOptionId;

    public function __construct(
        Uuid $subscriptionProductId,
        Uuid $tierId,
        Uuid $paymentOptionId
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->paymentOptionId = $paymentOptionId;
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
     * @return Uuid
     */
    public function getPaymentOptionId()
    {
        return $this->paymentOptionId;
    }
}
