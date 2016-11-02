<?php
namespace Ntech\Subscriptions\Models\SubscriptionProduct;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOption;
use Ntech\Subscriptions\Products\Tiers\PaymentOptions\TierPaymentOptionInfo;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions_products_tiers_payment_options")
 */
class SubscriptionProductTierPaymentOptionModel
{
    /**
     * @var Uuid
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
    **/
    private $id;
    /**
     * @var SubscriptionProductTierModel
     * @ORM\ManyToOne(targetEntity="Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierModel",
     *     inversedBy="paymentOptions")
     * @ORM\JoinColumn(name="tier_id", referencedColumnName="id", onDelete="CASCADE")
    **/
    private $tier;
    /**
     * @var TierPaymentOptionInfo
     * @ORM\Column(name="info", type="json_array")
    **/
    private $info;
    /**
     * @var SubscriptionTerms
     * @ORM\Column(name="subscription_terms", type="json_array")
    **/
    private $subscriptionTerms;

    public function __construct(
        TierPaymentOption $tierPaymentOption,
        SubscriptionProductTierModel $tier
    ) {
        $this->id = $tierPaymentOption->getId();
        $this->tier = $tier;
        $this->info = $tierPaymentOption->getInfo()->serialize();
        $this->subscriptionTerms = $tierPaymentOption->getTerms()->serialize();
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return Uuid::fromString($this->id);
    }

    /**
     * @return SubscriptionProductTierModel
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * @return SubscriptionTerms
     */
    public function getTerms()
    {
        return SubscriptionTerms::deserialize($this->subscriptionTerms);
    }

    /**
     * @param SubscriptionTerms $subscriptionTerms
     */
    public function setTerms(SubscriptionTerms $subscriptionTerms)
    {
        $this->subscriptionTerms = $subscriptionTerms->serialize();
    }

    /**
     * @return TierPaymentOptionInfo
     */
    public function getInfo()
    {
        return TierPaymentOptionInfo::deserialize($this->info);
    }

    /**
     * @param TierPaymentOptionInfo $info
     */
    public function setInfo(TierPaymentOptionInfo $info)
    {
        $this->info = $info->serialize();
    }


}
