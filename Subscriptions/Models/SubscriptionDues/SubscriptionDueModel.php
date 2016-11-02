<?php
namespace Ntech\Subscriptions\Models\SubscriptionDues;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel;
use Ntech\Subscriptions\Dues\SubscriptionDue;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions_dues")
 */
class SubscriptionDueModel
{
    /**
     * @var Uuid
     * @ORM\Column(name="subscription_id", type="guid")
     * @ORM\Id
     */
    private $subscriptionId;
    /**
     * The subscription period integer ID
     * @var int
     * @ORM\Column(name="period_id", type="integer")
     * @ORM\Id
     */
    private $periodId;
    /**
     * @var int
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;
    /**
     * @var string
     * @ORM\Column(name="currency", type="string", length=4)
     */
    private $currency;
    /**
     * @var InvoiceDoctrineModel
     * @ORM\OneToOne(targetEntity="Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel")
     */
    private $invoice;

    public function __construct(
        Uuid $subscriptionId,
        int $periodId,
        SubscriptionDue $due
    ) {
        $this->subscriptionId = $subscriptionId->toString();
        $this->periodId = $periodId;
        $this->amount = $due->getAmount()->getAmount();
        $this->currency = $due->getAmount()->getCurrencyCode();
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return Uuid::fromString($this->subscriptionId);
    }

    /**
     * @return int
     */
    public function getPeriodId()
    {
        return $this->periodId;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return new Amount($this->amount, $this->currency);
    }
    
    public function setInvoice(InvoiceDoctrineModel $invoiceDoctrineModel)
    {
        $this->invoice = $invoiceDoctrineModel;
    }

    /**
     * @return InvoiceDoctrineModel
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

}
