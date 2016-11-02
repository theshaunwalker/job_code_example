<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\Command;
use Ntech\Subscriptions\Periods\SubscriptionPeriod;
use Ntech\Uuid\Uuid;

class GenerateInvoiceForSubscriptionPeriodCommand extends Command
{
    /**
     * @var Uuid
     */
    private $invoiceId;
    /**
     * @var SubscriptionPeriod
     */
    private $subscriptionPeriod;

    public function __construct(
        Uuid $invoiceId,
        SubscriptionPeriod $subscriptionPeriod
    ) {
        $this->invoiceId = $invoiceId;
        $this->subscriptionPeriod = $subscriptionPeriod;
    }

    /**
     * @return Uuid
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @return SubscriptionPeriod
     */
    public function getSubscriptionPeriod()
    {
        return $this->subscriptionPeriod;
    }

}
