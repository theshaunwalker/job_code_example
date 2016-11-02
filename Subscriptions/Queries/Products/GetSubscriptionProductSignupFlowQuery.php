<?php
namespace Ntech\Subscriptions\Queries\Products;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class GetSubscriptionProductSignupFlowQuery implements Query
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var string
     */
    private $flowReference;

    public function __construct(
        Uuid $subscriptionProductId,
        string $flowReference
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->flowReference = $flowReference;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return string
     */
    public function getFlowReference()
    {
        return $this->flowReference;
    }
}
