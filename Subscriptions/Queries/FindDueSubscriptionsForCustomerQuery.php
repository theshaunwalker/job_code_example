<?php
namespace Ntech\Subscriptions\Queries;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\PaginatedFindQueryTrait;
use NtechUtility\Cqrs\Query\Query;

class FindDueSubscriptionsForCustomerQuery implements Query
{
    use PaginatedFindQueryTrait;
    /**
     * @var Uuid
     */
    private $customerId;

    public function __construct(
        Uuid $customerId
    ) {
        $this->customerId = $customerId;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
