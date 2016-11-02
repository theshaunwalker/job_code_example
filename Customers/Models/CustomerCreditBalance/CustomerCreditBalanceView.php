<?php
namespace Ntech\Customers\Models\CustomerCreditBalance;

use Doctrine\ORM\EntityManager;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;

class CustomerCreditBalanceView
{
    /**
     * @var Amount
     */
    private $credit;

    public function __construct(
        Amount $credit
    ) {
        $this->credit = $credit;
    }

    public static function fromDoctrine(
        EntityManager $em,
        Uuid $customerId
    ) {
        $balanceEntity = $em->getRepository(CustomerCreditDoctrineModel::class)
            ->findOneBy([
                'customerId' => $customerId->toString()
            ]);
        return new static($balanceEntity->getCredit());
    }

    /**
     * @return Amount
     */
    public function getCredit()
    {
        return $this->credit;
    }

}
