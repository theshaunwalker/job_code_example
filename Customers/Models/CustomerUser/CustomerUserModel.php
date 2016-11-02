<?php
namespace Ntech\Customers\Models\CustomerUser;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Users\Models\SingleDoctrine\UserDoctrineModel;
use Ntech\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="customer_user")
 */
class CustomerUserModel
{
    /**
     * @var CustomerDoctrineModel
     * @ORM\ManyToOne(targetEntity="Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel", inversedBy="customerUsers")
     * @ORM\Id
    **/
    private $customer;
    /**
     * @var UserDoctrineModel
     * @ORM\ManyToOne(targetEntity="Ntech\Users\Models\SingleDoctrine\UserDoctrineModel", inversedBy="customerUsers")
     * @ORM\Id
    **/
    private $user;

    public function __construct(
        CustomerDoctrineModel $customer,
        UserDoctrineModel $user
    ) {
        $this->customer = $customer;
        $this->user = $user;
    }

    /**
     * @return CustomerDoctrineModel
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    public function getCustomerId()
    {
        return $this->customer->getId();
    }

    /**
     * @return UserDoctrineModel
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getUserId()
    {
        return $this->user->getId();
    }
    
}
