<?php
namespace Ntech\Subscriptions\Models\SubscriptionProduct;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Subscriptions\Products\SubscriptionProductInfo;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;
use NtechUtility\Support\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions_products")
 */
class SubscriptionProductModel
{
    /**
     * @var Uuid
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     */
    private $id;
    /**
     * @var CompanyDoctrineModel
     * @ORM\ManyToOne(targetEntity="Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel", fetch="EAGER")
     */
    private $company;
    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    /**
     * @var string
     * @ORM\Column(name="blurb", type="string")
    **/
    private $blurb;
    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean")
    **/
    private $active = true;
    /**
     * @var Carbon
     * @ORM\Column(name="created_on", type="datetime")
    **/
    private $createdOn;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel", mappedBy="subscriptionProduct")
     */
    private $subscriptions;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierModel", mappedBy="subscriptionProduct")
    **/
    private $tiers;

    public function __construct(
        Uuid $id,
        $company,
        SubscriptionProductInfo $productInfo,
        Carbon $createdOn
    ) {
        $this->id = $id;
        $this->company = $company;
        $this->name = $productInfo->getName();
        $this->description = $productInfo->getDescription();
        $this->blurb = $productInfo->getBlurb();
        $this->createdOn = $createdOn;
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

    /**
     * @return Uuid
     */
    public function getCompanyId()
    {
        return $this->getCompany()->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getBlurb()
    {
        return $this->blurb;
    }

    /**
     * @param string $blurb
     */
    public function setBlurb($blurb)
    {
        $this->blurb = $blurb;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return Carbon
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function modifyInfo(SubscriptionProductInfo $info)
    {
        $this->setName($info->getName());
        $this->setDescription($info->getDescription());
        $this->setBlurb($info->getBlurb());
    }

    /**
     * @return ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @return ArrayCollection
     */
    public function getTiers()
    {
        return $this->tiers;
    }
    
    public function getTiersCollection()
    {
        return new SubscriptionProductTierCollection($this->getTiers()->toArray());
    }

    public function getTier(Uuid $tierId)
    {
        return (new Collection($this->tiers->toArray()))
            ->first(function ($key, $model) use ($tierId) {
                return $model->getId() == $tierId;
            });
    }
    

}
