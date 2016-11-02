<?php
namespace Ntech\Customers\Queries;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class GetCustomerAccountSelectionViewForUserQuery implements Query
{
    /**
     * @var Uuid
     */
    private $userId;

    public function __construct(
        Uuid $userId
    ) {
        $this->userId = $userId;
    }

    /**
     * @return Uuid
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
