<?php
namespace Ntech\Subscriptions\Tasks;

use Ntech\Uuid\Uuid;
use NtechUtility\Tasks\Task;

class PayOutstandingSubscriptionInvoicesTask implements Task
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
