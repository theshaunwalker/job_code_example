<?php
namespace Ntech\Subscriptions\Models\SubscriptionProduct;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ntech\Subscriptions\Products\Tiers\Tier;
use Ntech\Subscriptions\Products\Tiers\TierInfo;
use Ntech\Uuid\Uuid;
use NtechUtility\Support\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions_products_tiers")
 */
class SubscriptionProductTierModel
{
    /**
     * @var Uuid
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
    **/
    private $id;
    /**
     * @var SubscriptionProductModel
     * @ORM\ManyToOne(targetEntity="Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel",
     *     inversedBy="tiers")
    **/
    private $subscriptionProduct;
    /**
     * @var TierInfo
     * @ORM\Column(name="info", type="json_array")
    **/
    private $info;
    /**
     * @var bool
     * @ORM\Column(name="retired", type="boolean")
    **/
    private $retired = false;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierPaymentOptionModel",
     *     mappedBy="tier")
    **/
    private $paymentOptions;

    public function __construct(
        Tier $tier,
        $subscriptionProduct
    ) {
        $this->id = $tier->getId();
        $this->subscriptionProduct = $subscriptionProduct;
        $this->info = $tier->getInfo()->serialize();
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return Uuid::fromString($this->id);
    }

    /**
     * @return SubscriptionProductModel
     */
    public function getSubscriptionProduct()
    {
        return $this->subscriptionProduct;
    }

    /**
     * @return TierInfo
     */
    public function getInfo()
    {
        return TierInfo::deserialize($this->info);
    }

    /**
     * @param TierInfo $info
     */
    public function setInfo($info)
    {
        $this->info = $info->serialize();
    }

    /**
     * @return boolean
     */
    public function isRetired()
    {
        return $this->retired;
    }

    /**
     * @param boolean $retired
     */
    public function setRetired($retired)
    {
        $this->retired = $retired;
    }

    /**
     * @return ArrayCollection
     */
    public function getPaymentOptions()
    {
        return $this->paymentOptions;
    }

    public function getPaymentOptionsCollection()
    {
        return new Collection($this->paymentOptions->toArray());
    }
    
    public function getPaymentOption(Uuid $paymentOptionId)
    {
        return (new Collection($this->paymentOptions->toArray()))
            ->first(function ($key, $model) use ($paymentOptionId) {
                return $model->getId() == $paymentOptionId;
            });
    }

    public function getName()
    {
        return $this->getInfo()->getName();
    }

}
