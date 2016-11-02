<?php
namespace Ntech\Subscriptions\Events;

use Ntech\Events\Event;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionDueInvoiceGenerated extends Event implements Serializable
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
     * @var Uuid
     */
    private $invoiceId;

    public function __construct(
        Uuid $subscriptionId,
        int $periodId,
        Uuid $invoiceId
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->periodId = $periodId;
        $this->invoiceId = $invoiceId;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            $data['periodId'],
            Uuid::fromString($data['invoiceId'])
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
            'invoiceId' => $this->invoiceId->toString()
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
     * @return Uuid
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }
}
