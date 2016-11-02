<?php
namespace Ntech\Customers\Queries;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class GetCustomerDashboardPaymentSummaryQuery implements Query
{
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
