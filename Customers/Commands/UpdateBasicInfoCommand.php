<?php
namespace Ntech\Customers\Commands;

use Carbon\Carbon;
use Ntech\CommandBus\Command;
use Ntech\Customers\CustomerBasicInfo;
use Ntech\Uuid\Uuid;

class UpdateBasicInfoCommand extends Command
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var CustomerBasicInfo
     */
    private $customerBasicInfo;

    public function __construct(
        Uuid $customerId,
        CustomerBasicInfo $customerBasicInfo
    ) {
        $this->customerId = $customerId;
        $this->customerBasicInfo = $customerBasicInfo;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return CustomerBasicInfo
     */
    public function getCustomerBasicInfo()
    {
        return $this->customerBasicInfo;
    }
}
