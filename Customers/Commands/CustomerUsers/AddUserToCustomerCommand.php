<?php
namespace Ntech\Customers\Commands\CustomerUsers;

use Ntech\Uuid\Uuid;

class AddUserToCustomerCommand
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $userId;

    public function __construct(
        Uuid $customerId,
        Uuid $userId
    ) {
        $this->customerId = $customerId;
        $this->userId = $userId;
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
    public function getUserId()
    {
        return $this->userId;
    }
}
