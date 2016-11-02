<?php
namespace Ntech\Customers\Queries;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class GetCustomerByCompanyAndEmailQuery implements Query
{
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var string
     */
    private $email;

    public function __construct(
        Uuid $companyId,
        string $email
    ) {
        $this->companyId = $companyId;
        $this->email = $email;
    }

    /**
     * @return Uuid
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
