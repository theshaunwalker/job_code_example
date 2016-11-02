<?php
namespace Ntech\Subscriptions\Queries;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class GetSubscriptionQuery implements Query
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
