<?php
namespace Ntech\Customers\Queries;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class GetCustomerAccountSelectionViewForCompanyQuery implements Query
{
    /**
     * @var Uuid
     */
    private $companyId;

    public function __construct(
        Uuid $companyId
    ) {
        $this->companyId = $companyId;
    }

    /**
     * @return Uuid
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }
}
