<?php
namespace Ntech\Subscriptions\Dues;

use Ntech\Subscriptions\Periods\SubscriptionPeriod;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;
use NtechUtility\Serializer\Serializable;

class SubscriptionDue implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var int
     */
    private $periodId;
    /**
     * @var Amount
     */
    private $amount;
    /**
     * @var Uuid
     */
    private $invoiceId;

    public function __construct(
        Uuid $subscriptionId,
        int $periodId,
        Amount $amount
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->periodId = $periodId;
        $this->amount = $amount;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            $data['periodId'],
            new Amount($data['amount'], $data['currency']),
            $data['invoiceId'] == null ? null : Uuid::fromString($data['invoiceId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString(),
            'periodId' => $this->periodId,
            'amount' => $this->amount->getAmount(),
            'currency' => $this->amount->getCurrencyCode(),
            'invoiceId' => $this->invoiceId == null ? null : $this->invoiceId->toString()
        ];
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @return int
     */
    public function getPeriodId()
    {
        return $this->periodId;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Uuid
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(Uuid $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }
}
