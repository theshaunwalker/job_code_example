<?php
namespace Ntech\Subscriptions\Products;

use Ntech\Exceptions\DomainException;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierCreated;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierDeleted;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierInfoModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionCreated;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionDeleted;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionInfoModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionTermsModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierRetired;
use Ntech\Subscriptions\Exceptions\Products\SubscriptionProductException;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOption;
use Ntech\Subscriptions\Products\Tiers\Tier;
use NtechUtility\EventSource\AggregateRootAlreadyRegisteredException;
use NtechUtility\EventSource\EventSourcedEntity;
use NtechUtility\EventSource\EventSourcedEntityTrait;
use NtechUtility\Support\Collections\Collection;

class SubscriptionProductTiers implements EventSourcedEntity
{
    use EventSourcedEntityTrait;

    /**
     * Collection of tier objects
     * @var Collection
     */
    private $tiers;

    /**
     * Collection of tier payment options keyed by tier ID
     * @var Collection
     */
    private $paymentOptions;
    
    public function __construct()
    {
        $this->tiers = new Collection();
        $this->paymentOptions = new Collection();
    }

    public function applySubscriptionProductTierCreated(SubscriptionProductTierCreated $event)
    {
        $tier = $event->getTier();
        if ($this->tiers->get($tier->getId()->toString()) != null) {
            throw SubscriptionProductException::because("Tier already added to subscription");
        }
        $this->tiers->put($tier->getId()->toString(), $tier);
    }
    
    public function applySubscriptionProductTierDeleted(SubscriptionProductTierDeleted $event)
    {
        $this->tiers->pull($event->getTierId()->toString(), null);
    }
    
    public function applySubscriptionProductTierRetired(SubscriptionProductTierRetired $event)
    {
        /** @var Tier $tier */
        $tier = $this->tiers->get($event->getTierId()->toString());

        $modifiedTier = new Tier(
            $tier->getId(),
            $tier->getSubscriptionProductId(),
            $tier->getInfo(),
            true
        );
        $this->tiers->put($event->getTierId()->toString(), $modifiedTier);
    }

    public function applySubscriptionProductTierPaymentOptionCreated(SubscriptionProductTierPaymentOptionCreated $event)
    {
        $tierId = $event->getTierId();
        $paymentOptionId = $event->getPaymentOption()->getId();
        if ($this->paymentOptions->has($tierId->toString())) {
            if ($this->paymentOptions->has($paymentOptionId->toString())) {
                throw DomainException::because("Duplicate ID. Tier Payment Option already exists for ID [{$paymentOptionId->toString()}]");
            }
        } else {
            $this->paymentOptions->put($tierId->toString(), new Collection());
        }
        $tierPaymentOptions = $this->paymentOptions->get($tierId);
        $tierPaymentOptions->put(
            $paymentOptionId->toString(),
            $event->getPaymentOption()
        );
    }

    public function applySubscriptionProductTierPaymentOptionDeleted(SubscriptionProductTierPaymentOptionDeleted $event)
    {
        $tierPaymentOptions = $this->paymentOptions->get($event->getTierId()->toString());

        $tierPaymentOptions->pull(
            $event->getPaymentOptionId()->toString()
        );
    }

    public function applySubscriptionProductTierPaymentOptionTermsModified(SubscriptionProductTierPaymentOptionTermsModified $event)
    {
        $tierPaymentOptions = $this->paymentOptions->get($event->getTierId()->toString());
        $paymentOption = $tierPaymentOptions->get($event->getPaymentOptionId()->toString());

        $newPaymentOption = new TierPaymentOption(
            $paymentOption->getId(),
            $paymentOption->getInfo(),
            $event->getNewTerms()
        );
        $tierPaymentOptions->put($paymentOption->getId()->toString(), $newPaymentOption);
    }

    public function applySubscriptionProductTierPaymentOptionInfoModified(SubscriptionProductTierPaymentOptionInfoModified $event)
    {
        $tierPaymentOptions = $this->paymentOptions->get($event->getTierId()->toString());
        $paymentOption = $tierPaymentOptions->get($event->getPaymentOptionId()->toString());

        $newPaymentOption = new TierPaymentOption(
            $paymentOption->getId(),
            $event->getNewInfo(),
            $paymentOption->getTerms()
        );
        $tierPaymentOptions->put($paymentOption->getId()->toString(), $newPaymentOption);
    }
    
    public function applySubscriptionProductTierInfoModified(SubscriptionProductTierInfoModified $event)
    {
        $tier = $this->tiers->get($event->getTierId()->toString());

        $modifiedTier = new Tier(
            $tier->getId(),
            $tier->getSubscriptionProductId(),
            $event->getTierInfo(),
            $tier->isRetired()
        );
        $this->tiers->put($event->getTierId()->toString(), $modifiedTier);
    }
}
