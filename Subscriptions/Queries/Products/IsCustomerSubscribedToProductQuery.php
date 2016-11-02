<?php
namespace Ntech\Subscriptions\Queries\Products;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class IsCustomerSubscribedToProductQuery implements Query
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $subscriptionProductId;

    public function __construct(
        Uuid $customerId,
        Uuid $subscriptionProductId
    ) {
        $this->customerId = $customerId;
        $this->subscriptionProductId = $subscriptionProductId;
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
}
