<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class StartSubscriptionFromProductCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var Uuid
     */
    private $customerId;
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
    private $tierPaymentOptionId;

    public function __construct(
        Uuid $subscriptionId,
        Uuid $customerId,
        Uuid $subscriptionProductId,
        Uuid $tierId,
        Uuid $tierPaymentOptionId
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->customerId = $customerId;
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->tierPaymentOptionId = $tierPaymentOptionId;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
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
    public function getTierPaymentOptionId()
    {
        return $this->tierPaymentOptionId;
    }
}
