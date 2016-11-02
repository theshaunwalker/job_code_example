<?php
namespace Ntech\Customers\Queries;

use Carbon\Carbon;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class GetCustomerInvoiceSummaryQuery implements Query
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Carbon
     */
    private $windowDate;
    /**
     * @var int
     */
    private $totalRecentCount;

    public function __construct(
        Uuid $customerId,
        Carbon $windowDate,
        int $totalRecentCount
    ) {
        $this->customerId = $customerId;
        $this->windowDate = $windowDate;
        $this->totalRecentCount = $totalRecentCount;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return Carbon
     */
    public function getWindowDate()
    {
        return $this->windowDate;
    }

    /**
     * @return int
     */
    public function getTotalRecentCount()
    {
        return $this->totalRecentCount;
    }
}
