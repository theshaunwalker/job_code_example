<?php
namespace Ntech\Subscriptions;

use Carbon\Carbon;
use Ntech\CustomFields\EventSource\AggregateEntityHasCustomFieldValuesTrait;
use Ntech\CustomFields\EventSource\EntityHasCustomFieldValues;
use Ntech\Exceptions\DomainException;
use Ntech\Invoices\Invoice;
use Ntech\Payments\PaymentSubscription;
use Ntech\Subscriptions\Dues\SubscriptionDue;
use Ntech\Subscriptions\Dues\SubscriptionDueCollection;
use Ntech\Subscriptions\Dues\SubscriptionDues;
use Ntech\Subscriptions\Events\SubscriptionAttachedToSubscriptionProduct;
use Ntech\Subscriptions\Events\SubscriptionDueGeneratedForPeriod;
use Ntech\Subscriptions\Events\PaymentMethodAttachedToSubscription;
use Ntech\Subscriptions\Events\SubscriptionCancelled;
use Ntech\Subscriptions\Events\SubscriptionDueInvoiceGenerated;
use Ntech\Subscriptions\Events\SubscriptionExpirationRemoved;
use Ntech\Subscriptions\Events\SubscriptionPaymentMade;
use Ntech\Subscriptions\Events\SubscriptionPeriodStarted;
use Ntech\Subscriptions\Events\SubscriptionReactivated;
use Ntech\Subscriptions\Events\SubscriptionRenewed;
use Ntech\Subscriptions\Events\SubscriptionSetToExpire;
use Ntech\Subscriptions\Events\SubscriptionStarted;
use Ntech\Subscriptions\Events\SubscriptionSuspended;
use Ntech\Subscriptions\Exceptions\SubscriptionCannotBeCancelled;
use Ntech\Subscriptions\Exceptions\SubscriptionCannotBeReactivated;
use Ntech\Subscriptions\Exceptions\SubscriptionCannotBeSuspended;
use Ntech\Subscriptions\Exceptions\SubscriptionCannotBeRenewed;
use Ntech\Subscriptions\Payments\SubscriptionPayment;
use Ntech\Subscriptions\Payments\SubscriptionPaymentMethod;
use Ntech\Subscriptions\Periods\SubscriptionPeriod;
use Ntech\Subscriptions\Periods\SubscriptionPeriods;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use Ntech\Subscriptions\Terms\SubscriptionExpiration;
use Ntech\Uuid\Uuid;
use NtechUtility\EventSource\EventSourcedAggregateRoot;
use NtechUtility\EventSource\EventSourcedAggregateRootTrait;

class Subscription implements EventSourcedAggregateRoot, EntityHasCustomFieldValues
{
    use EventSourcedAggregateRootTrait;
    use AggregateEntityHasCustomFieldValuesTrait;

    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var Carbon
     */
    private $startDate;
    /**
     * @var SubscriptionTerms
     */
    private $terms;
    /**
     * @var SubscriptionExpiration
     */
    private $expiration;
    /**
     * @var Carbon
     */
    private $terminationDate;
    /**
     * @var SubscriptionPeriods
     */
    private $periods;

    const STATUS_ACTIVE = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_CANCELLED = 3;

    const PAYMENT_UP_TO_DATE = 1;
    const PAYMENT_DUE = 2;

    /**
     * One of the status constants
     * @var int
     */
    private $status;

    /**
     * Details on what the payment method for the subscription is
     * @var SubscriptionPaymentMethod
     */
    private $paymentMethod = null;

    /**
     * @var SubscriptionDues
     */
    private $dues;

    /**
     * @var Uuid
     */
    private $subscriptionProduct;

    /**
     * @return Uuid
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }

    public function getChildEntities()
    {
        return [
            $this->periods,
            $this->dues
        ];
    }

    public static function started(
        Uuid $subscriptionId,
        Uuid $companyId,
        Uuid $customerId,
        string $name,
        Carbon $startDate,
        SubscriptionTerms $terms,
        bool $prepay = true
    ) {
        $subscription = new self();
        $subscription->apply(
            new SubscriptionStarted(
                $subscriptionId,
                $companyId,
                $customerId,
                $name,
                $startDate,
                $terms,
                $prepay
            )
        );
        // If the start date is now or in the past initialize the first period
        // otherwise its starting in the future and we're going to let the renewal
        // process deal with it
        if ($startDate <= Carbon::now()) {
            $subscription->initializePeriods();
        }
        return $subscription;
    }

    public function applySubscriptionStarted(SubscriptionStarted $event)
    {
        $this->id = $event->getSubscriptionId();
        $this->companyId = $event->getCompanyId();
        $this->customerId = $event->getCustomerId();
        $this->startDate = $event->getStartDate();
        $this->terms = $event->getSubscriptionTerms();
        $this->name = $event->getName();
        $this->periods = new SubscriptionPeriods();
        $this->status = self::STATUS_ACTIVE;
        $this->dues = new SubscriptionDues(new SubscriptionDueCollection());
    }

    public function initializePeriods()
    {
        $firstPeriod = SubscriptionPeriod::fromSubscriptionTerms(
            $this->id,
            1,
            $this->startDate,
            $this->terms
        );
        $this->apply(
            new SubscriptionPeriodStarted(
                $this->id,
                $firstPeriod
            )
        );
    }

    /**
     * Set the subscription to expire according to the passed SubscriptionExpiration
     * @param SubscriptionExpiration $expiration
     */
    public function willExpire(SubscriptionExpiration $expiration)
    {

        $this->apply(
            new SubscriptionSetToExpire(
                $this->id,
                $expiration
            )
        );
    }

    public function applySubscriptionSetToExpire(SubscriptionSetToExpire $event)
    {
        $this->expiration = $event->getExpiration();
    }

    /**
     * Remove the set expiration for the subscription.
     * Leaving it endless.
     * Throws exception
     */
    public function removeExpiration()
    {
        // We dont do anything if there is no expiration
        if ($this->expiration == null) {
            return;
        }
        $this->apply(
            new SubscriptionExpirationRemoved(
                $this->id
            )
        );
    }

    public function applySubscriptionExpirationRemoved(SubscriptionExpirationRemoved $event)
    {
        $this->expiration = null;
    }

    /**
     * Cycle the subscription to the next period
     */
    public function renew()
    {
        if ($this->status != self::STATUS_ACTIVE) {
            throw SubscriptionCannotBeRenewed::whenNotActive();
        }
        $this->periods->cycleToNextPeriod(
            $this->terms
        );
        $this->apply(
            new SubscriptionRenewed($this->id)
        );
    }
    
    public function suspend(string $reason)
    {
        if ($this->status != self::STATUS_ACTIVE) {
            throw SubscriptionCannotBeSuspended::whenItsNotActive();
        } elseif ($this->status == self::STATUS_SUSPENDED) {
            throw SubscriptionCannotBeSuspended::whenItsAlreadySuspended();
        }
        $this->apply(
            new SubscriptionSuspended(
                $this->id,
                $reason
            )
        );
    }
    
    public function applySubscriptionSuspended(SubscriptionSuspended $event)
    {
        $this->status = self::STATUS_SUSPENDED;
    }

    public function cancel(string $reason)
    {
        if ($this->status == self::STATUS_CANCELLED) {
            throw SubscriptionCannotBeCancelled::whenAlreadyCancelled();
        }
        $this->apply(
            new SubscriptionCancelled(
                $this->id,
                $reason
            )
        );
    }

    public function applySubscriptionCancelled(SubscriptionCancelled $event)
    {
        $this->status = self::STATUS_CANCELLED;
    }
    
    public function reactivate()
    {
        if ($this->status == self::STATUS_CANCELLED) {
            throw SubscriptionCannotBeReactivated::whenAlreadyCancelled();
        } elseif ($this->status == self::STATUS_ACTIVE) {
            throw SubscriptionCannotBeReactivated::whenItsAlreadyActive();
        }
        
        $this->apply(
            new SubscriptionReactivated(
                $this->id
            )
        );
    }

    public function applySubscriptionReactivated(SubscriptionReactivated $event)
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function attachPaymentMethod(
        PaymentSubscription $paymentMethod
    ) {
        $this->apply(
            new PaymentMethodAttachedToSubscription(
                $this->id,
                $paymentMethod->getAggregateRootId()
            )
        );
    }
    
    public function applyPaymentMethodAttachedToSubscription(PaymentMethodAttachedToSubscription $event)
    {
        $this->paymentMethod = $event->getPaymentSubscriptionId();
    }

    public function duesNeededForPeriod(
        SubscriptionPeriod $period,
        $usage = null
    ) {
        if ($this->dues->getDues()->get($period->getOrderCount(), null) != null) {
            throw DomainException::because("Trying to generate subscription dues for a period that already has dues. 
                Subscription [{$this->id->toString()}], period [{$period->getOrderCount()}].");
        }
        if ($this->terms->isPrepay()) {
            $dueAmount = $this->terms->getRate();
        } else {
            // Calculate cost from usage
        }
        $due = new SubscriptionDue(
            $this->getAggregateRootId(),
            $period->getOrderCount(),
            $dueAmount
        );
        $this->apply(
            new SubscriptionDueGeneratedForPeriod(
                $this->id,
                $period->getOrderCount(),
                $due
            )
        );
    }

    public function setDueInvoice(
        int $periodId,
        Invoice $invoice
    ) {
        $this->apply(
            new SubscriptionDueInvoiceGenerated(
                $this->id,
                $periodId,
                $invoice->getAggregateRootId()
            )
        );
    }

    public function makePayment(
        Uuid $paymentId
    ) {
        $this->apply(
            new SubscriptionPaymentMade(
                $this->id,
                $paymentId
            )
        );
    }

    public function isForSubscriptionProduct(Uuid $subscriptionProductId, Uuid $tierId, Uuid $paymentOptionId)
    {
        $this->apply(
            new SubscriptionAttachedToSubscriptionProduct(
                $this->id,
                $subscriptionProductId,
                $tierId,
                $paymentOptionId
            )
        );
    }

    public function applySubscriptionAttachedToSubscriptionProduct(SubscriptionAttachedToSubscriptionProduct $event)
    {
        $this->subscriptionProduct = $event->getSubscriptionProductId();
    }

}
