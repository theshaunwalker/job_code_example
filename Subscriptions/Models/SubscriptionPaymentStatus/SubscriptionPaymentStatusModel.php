<?php
namespace Ntech\Subscriptions\Models\SubscriptionPaymentStatus;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Subscriptions\Subscription;
use NtechUtility\Money\Amount;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="subscriptions_payment_status")
 */
class SubscriptionPaymentStatusModel
{
    /**
     * @var SubscriptionDoctrineModel
     * @ORM\ManyToOne(targetEntity="Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel", inversedBy="paymentStatus")
     * @ORM\Id
    **/
    private $subscription;
    /**
     * @var integer
     * @ORM\Column(name="debit", type="integer")
    **/
    private $debit;
    /**
     * @var integer
     * @ORM\Column(name="credit", type="integer")
    **/
    private $credit;
    /**
     * @var string
     * @ORM\Column(name="currency", type="string")
    **/
    private $currency;
    /**
     * @var integer
     * @ORM\Column(name="status", type="integer")
    **/
    private $status;
    
    public function __construct(
        SubscriptionDoctrineModel $subscription,
        Amount $debit,
        Amount $credit
    ) {
        $this->subscription = $subscription;
        $this->debit = $debit;
        $this->credit = $credit;
        $this->currency = $this->debit->getCurrencyCode();
    }

    /**
     * @return SubscriptionDoctrineModel
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return int
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * @return int
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function refreshStatus()
    {
        switch ($this->debit <=> $this->credit) {
            case 1:
                $this->status = Subscription::PAYMENT_DUE;
                break;
            case 0:
            case -1:
                $this->status = Subscription::PAYMENT_UP_TO_DATE;
                break;
        }
    }

    public function addDebit(Amount $amount)
    {
        $this->debit = $this->debit->add($amount);
    }
    
    public function addCredit(Amount $amount)
    {
        $this->credit = $this->credit->add($amount);
    }
}
