<?php
namespace Ntech\Subscriptions\Tasks;

use Ntech\Uuid\Uuid;
use NtechUtility\Tasks\Task;

class RenewCompanySubscriptionsTask implements Task
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
