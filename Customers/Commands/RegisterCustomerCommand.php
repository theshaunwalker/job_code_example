<?php
namespace Ntech\Customers\Commands;

use Carbon\Carbon;
use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class RegisterCustomerCommand extends Command
{

    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var string
     */
    private $customerName;
    /**
     * @var Carbon
     */
    private $customerSince;
    /**
     * @var string
     */
    private $email;

    public function __construct(
        Uuid $customerId,
        Uuid $companyId,
        string $customerName,
        Carbon $customerSince,
        string $email = null
    ) {
        $this->customerId = $customerId;
        $this->companyId = $companyId;
        $this->customerName = $customerName;
        $this->customerSince = $customerSince;
        $this->email = $email;
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
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @return Carbon
     */
    public function getCustomerSince()
    {
        return $this->customerSince;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

}
