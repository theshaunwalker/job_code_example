<?php
namespace Ntech\Customers\Models\CustomerCreditBalance;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;

/**
 * @ORM\Table(name="customer_credit_balance")
 * @ORM\Entity
 */
class CustomerCreditDoctrineModel
{
    /**
     * @ORM\Column(name="customer_id", type="guid")
     * @ORM\Id
     */
    private $customerId;

    /**
     * @ORM\Column(name="credit", type="bigint")
     */
    private $credit;

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return Uuid::fromString($this->customerId);
    }

    /**
     * @return Amount
     */
    public function getCredit()
    {
        return new Amount((int)$this->credit, 'gbp');
    }


}
