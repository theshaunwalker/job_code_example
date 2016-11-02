<?php
namespace Ntech\Subscriptions\Models\Subscription;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Exceptions\NtechException;
use Ntech\Payments\Methods\PaymentMethodSubscription;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentMethodDoctrineModel;
use Ntech\Payments\Models\PaymentSubscription\PaymentSubscriptionModel;
use Ntech\Subscriptions\Models\SubscriptionPaymentStatus\SubscriptionPaymentStatusModel;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierModel;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierPaymentOptionModel;
use Ntech\Subscriptions\Models\Traits\ProvideSubscriptionStatusHtmlBadgeData;
use Ntech\Subscriptions\Subscription;
use Ntech\Subscriptions\SubscriptionPaymentDetails;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Subscriptions\Terms\SubscriptionExpiration;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions")
 */
class SubscriptionDoctrineModel
{
    /**
     * @var Uuid
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     */
    private $id;
    /**
     * @var CompanyDoctrineModel
     * @ORM\ManyToOne(targetEntity="Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel", inversedBy="subscriptions")
     */
    private $company;
    /**
     * @var CustomerDoctrineModel
     * @ORM\ManyToOne(targetEntity="Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel", inversedBy="subscriptions")
     */
    private $customer;
    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(name="interval_type", type="string")
     */
    private $interval;
    /**
     * @var int
     * @ORM\Column(name="interval_value", type="integer")
     */
    private $intervalValue;
    /**
     * @var Amount
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;
    /**
     * @var string
     * @ORM\Column(name="currency", type="string", length=4);
     */
    private $currency;
    /**
     * @var Carbon
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;
    /**
     * @var bool
     * @ORM\Column(name="prepay", type="boolean")
     */
    private $prepay;
    /**
     * @var Carbon
     * @ORM\Column(name="renew_date", type="datetime", nullable=true)
     */
    private $renewalDate;
    /**
     * @var int
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    /**
     * @var int
     * @ORM\Column(name="current_period", type="integer", nullable=true)
     */
    private $currentPeriod;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Ntech\Subscriptions\Models\Subscription\SubscriptionPeriodDoctrineModel", mappedBy="subscription")
     */
    private $periods;
    /**
     * @var Carbon
     * @ORM\Column(name="expiration_date", type="datetime", nullable=true)
     */
    private $expirationDate;
    /**
     * @var int
     * @ORM\Column(name="expiration_intervals", type="integer", nullable=true)
     */
    private $expirationIntervals;

    /**
     * @var PaymentSubscriptionModel
     * @ORM\OneToOne(targetEntity="Ntech\Payments\Models\PaymentSubscription\PaymentSubscriptionModel")
     * @ORM\JoinColumn(name="payment_subscription_id")
     */
    private $paymentMethod;
    /**
     * @var SubscriptionProductModel
     * @ORM\ManyToOne(targetEntity="Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="subscription_product_id", referencedColumnName="id")
     */
    private $subscriptionProduct;
    /**
     * @var SubscriptionProductTierModel
     * @ORM\ManyToOne(targetEntity="Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierModel")
    **/
    private $tier;
    /**
     * @var SubscriptionProductTierPaymentOptionModel
     * @ORM\ManyToOne(targetEntity="Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierPaymentOptionModel")
    **/
    private $tierPaymentOption;
    /**
     * @var SubscriptionPaymentStatusModel
     * @ORM\OneToMany(targetEntity="Ntech\Subscriptions\Models\SubscriptionPaymentStatus\SubscriptionPaymentStatusModel", mappedBy="subscription")
    **/
    private $paymentStatus;

    use ProvideSubscriptionStatusHtmlBadgeData;

    public function __construct(
        Uuid $id,
        CompanyDoctrineModel $company,
        CustomerDoctrineModel $customer,
        string $name,
        Carbon $startDate,
        SubscriptionTerms $terms,
        int $status
    ) {
        $this->id = $id->toString();
        $this->company = $company;
        $this->customer = $customer;
        $this->name = $name;
        $this->amount = $terms->getRate()->getAmount();
        $this->currency = $terms->getRate()->getCurrencyCode();
        $this->interval = $terms->getInterval();
        $this->intervalValue = $terms->getIntervalCount();
        $this->startDate = $startDate;
        $this->renewalDate = $startDate;
        $this->prepay = $terms->isPrepay();
        $this->status = $status;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return Uuid::fromString($this->id);
    }

    /**
     * @return CompanyDoctrineModel
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function getCompanyId()
    {
        return $this->company->getId();
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @return int
     */
    public function getIntervalValue()
    {
        return $this->intervalValue;
    }

    /**
     * @return Carbon
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @return int
     */
    public function getExpirationIntervals()
    {
        return $this->expirationIntervals;
    }

    /**
     * @return boolean
     */
    public function isPrepay()
    {
        return $this->prepay;
    }

    /**
     * @return Carbon
     */
    public function getRenewalDate()
    {
        return $this->renewalDate;
    }

    /**
     * @param Carbon $renewalDate
     */
    public function setRenewalDate(Carbon $renewalDate)
    {
        $this->renewalDate = $renewalDate;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function isActive()
    {
        return $this->getStatus() == Subscription::STATUS_ACTIVE;
    }

    public function isSuspended()
    {
        return $this->getStatus() == Subscription::STATUS_SUSPENDED;
    }

    public function isCancelled()
    {
        return $this->getStatus() == Subscription::STATUS_CANCELLED;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getCurrentPeriod()
    {
        return $this->currentPeriod;
    }

    /**
     * @param int $currentPeriod
     */
    public function setCurrentPeriod(int $currentPeriod)
    {
        $this->currentPeriod = $currentPeriod;
    }

    public function setExpiration(SubscriptionExpiration $expiration)
    {
        $this->removeExpiration();
        switch ($expiration->getType()) {
            case SubscriptionExpiration::ON_DATE:
                $this->expirationDate = $expiration->getDate();
                break;
            case SubscriptionExpiration::BY_INTERVAL_COUNT:
                $this->expirationIntervals = $expiration->getIntervalCount();
                break;
        }
    }
    
    public function removeExpiration()
    {
        $this->expirationDate = null;
        $this->expirationIntervals = null;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return new Amount($this->amount, $this->currency);
    }

    public function isForAProduct()
    {
        return $this->subscriptionProduct != null;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod($paymentSubscriptionModel)
    {
        $this->paymentMethod = $paymentSubscriptionModel;
    }

    public function removePaymentMethodDetails()
    {
        $this->paymentMethod = null;
    }

    /**
     * Cant typehint the doctrine entities here because Doctrine isnt
     * lazy loading shit or using getReference() as a sensible person
     * would think it should work (it just passes the proxy because thats
     * whats in the ID map I guess? either way fuck Doctrine)
     *
     * @param $subscriptionProduct
     * @param $tierModel
     * @param $paymentOptionModel
     */
    public function setSubscriptionProduct(
        $subscriptionProduct,
        $tierModel,
        $paymentOptionModel
    ) {
        $this->subscriptionProduct = $subscriptionProduct;
        $this->tier = $tierModel;
        $this->tierPaymentOption = $paymentOptionModel;
    }

    /**
     * @return SubscriptionProductModel
     */
    public function getSubscriptionProduct()
    {
        return $this->subscriptionProduct;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProduct->getId();
    }

    /**
     * @return SubscriptionProductTierModel
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * @return Uuid
     */
    public function getTierId()
    {
        return $this->tier->getId();
    }

    /**
     * @return SubscriptionProductTierPaymentOptionModel
     */
    public function getTierPaymentOption()
    {
        return $this->tierPaymentOption;
    }

    /**
     * @return Uuid
     */
    public function getTierPaymentOptionId()
    {
        return $this->tierPaymentOption;
    }

    /**
     * @return SubscriptionTerms
     */
    public function getTerms()
    {
        return new SubscriptionTerms(
            $this->getAmount(),
            $this->getInterval(),
            $this->getIntervalValue(),
            $this->isPrepay()
        );
    }

    public function getBadgeDataStatus()
    {
        return $this->getStatus();
    }

    /**
     * Is the subscription currently in running term?
     * Regardless of whether the subscription has been cancelled or not
     * @return bool
     */
    public function isInTerm()
    {
        return $this->getRenewalDate() > Carbon::now();
    }

    public function getReadableTerms()
    {
        $terms = $this->getTerms();

        return $terms->getRate()->readable() . ' every ' . $terms->getIntervalCount() . ' ' . $terms->getReadableInterval() .
            " starting on " . $this->getRenewalDate()->format(config('ntech.system.date_format_short'));
    }
}
