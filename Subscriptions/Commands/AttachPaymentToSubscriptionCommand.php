<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class AttachPaymentToSubscriptionCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var Uuid
     */
    private $paymentId;

    public function __construct(
        Uuid $subscriptionId,
        Uuid $paymentId
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->paymentId = $paymentId;
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
    public function getPaymentId()
    {
        return $this->paymentId;
    }
}
