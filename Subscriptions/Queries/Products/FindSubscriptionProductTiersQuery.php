<?php
namespace Ntech\Subscriptions\Queries\Products;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\PaginatedFindQueryTrait;
use NtechUtility\Cqrs\Query\Query;

class FindSubscriptionProductTiersQuery implements Query
{
    use PaginatedFindQueryTrait;

    /**
     * @var Uuid
     */
    private $subscriptionProductId;

    public function __construct(
        Uuid $subscriptionProductId
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }
}
