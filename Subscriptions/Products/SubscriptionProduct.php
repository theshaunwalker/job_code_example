<?php
namespace Ntech\Subscriptions\Products;

use Ntech\CustomFields\EventSource\AggregateEntityHasCustomFieldsTrait;
use Ntech\CustomFields\EventSource\EntityHasCustomFields;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductCreated;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductInfoModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTermsModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierCreated;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierDeleted;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierInfoModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionCreated;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionDeleted;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionInfoModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionTermsModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierRetired;
use Ntech\Subscriptions\Products\SignupFlow\SignupSettings;
use Ntech\Subscriptions\Products\SignupFlow\SubscriptionProductSignupFlow;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOption;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOptionInfo;
use Ntech\Subscriptions\Products\Tiers\Tier;
use Ntech\Subscriptions\Products\Tiers\TierInfo;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;
use NtechUtility\Eav\Fields\FieldBag;
use NtechUtility\EventSource\EventSourcedAggregateRoot;
use NtechUtility\EventSource\EventSourcedAggregateRootTrait;

class SubscriptionProduct implements EventSourcedAggregateRoot, EntityHasCustomFields
{
    use EventSourcedAggregateRootTrait;
    use AggregateEntityHasCustomFieldsTrait;

    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var SubscriptionProductInfo
     */
    private $info;
    /**
     * @var SubscriptionProductTiers
     */
    private $tiers;
    
    /**
     * @return Uuid
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }
    
    protected function getChildEntities()
    {
        return [
            $this->tiers
        ];
    }

    public static function create(
        Uuid $id,
        Uuid $companyId,
        SubscriptionProductInfo $info
    ) {
        $product = new self();
        $product->apply(
            new SubscriptionProductCreated(
                $id,
                $companyId,
                $info
            )
        );
        return $product;
    }

    public function applySubscriptionProductCreated(SubscriptionProductCreated $event)
    {
        $this->id = $event->getId();
        $this->companyId = $event->getCompanyId();
        $this->info = $event->getInfo();
        $this->tiers = new SubscriptionProductTiers();
    }

    public function updateInfo(SubscriptionProductInfo $info)
    {
        $this->apply(
            new SubscriptionProductInfoModified(
                $this->id,
                $info
            )
        );
    }

    public function applySubscriptionProductInfoModified(SubscriptionProductInfoModified $event)
    {
        $this->info = $event->getInfo();
    }
    
    public function createTier(Tier $tier)
    {
        $this->apply(
            new SubscriptionProductTierCreated(
                $this->id,
                $tier
            )
        );
    }
    
    public function deleteTier(Uuid $tierId)
    {
        $this->apply(
            new SubscriptionProductTierDeleted(
                $this->id,
                $tierId
            )
        );
    }
    
    public function retireTier(Uuid $tierId)
    {
        $this->apply(
            new SubscriptionProductTierRetired(
                $this->id,
                $tierId
            )
        );
    }
    
    public function addPaymentOptionForTier(Uuid $tierId, TierPaymentOption $paymentOption)
    {
        $this->apply(
            new SubscriptionProductTierPaymentOptionCreated(
                $this->companyId,
                $this->id,
                $tierId,
                $paymentOption
            )
        );
    }
    
    public function removePaymentOptionFromTier(Uuid $tierId, Uuid $paymentOptionId)
    {
        $this->apply(
            new SubscriptionProductTierPaymentOptionDeleted(
                $this->companyId,
                $this->id,
                $tierId,
                $paymentOptionId
            )
        );
    }

    public function updatePaymentOptionTermsForTier(Uuid $tierId, Uuid $paymentOptionId, SubscriptionTerms $newTerms)
    {
        $this->apply(
            new SubscriptionProductTierPaymentOptionTermsModified(
                $this->companyId,
                $this->id,
                $tierId,
                $paymentOptionId,
                $newTerms
            )
        );
    }

    public function updatePaymentOptionInfoForTier(Uuid $tierId, Uuid $paymentOptionId, TierPaymentOptionInfo $newInfo)
    {
        $this->apply(
            new SubscriptionProductTierPaymentOptionInfoModified(
                $this->companyId,
                $this->id,
                $tierId,
                $paymentOptionId,
                $newInfo
            )
        );
    }
    
    public function updateTierInfo(Uuid $tierId, TierInfo $newInfo)
    {
        $this->apply(
            new SubscriptionProductTierInfoModified(
                $this->id,
                $tierId,
                $newInfo
            )
        );
    }
}
