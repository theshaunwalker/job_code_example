<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use Ntech\Payments\Processing\Gateways\CustomerAccountCredit\CustomerAccountCreditCharge;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;
use NtechUtility\Serializer\Serializable;

class CreditDebitedFromCustomer extends Event implements Serializable
{

    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Amount
     */
    private $amount;

    public function __construct(
        Uuid $customerId,
        Amount $amount
    ) {
        $this->customerId = $customerId;
        $this->amount = $amount;
    }
    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['customerId']),
            new Amount($data['amount'], $data['amount_currency'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'amount' => $this->amount->getAmount(),
            'amount_currency' => $this->amount->getCurrencyCode()
        ];
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

}
