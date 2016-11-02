<?php
namespace Ntech\Subscriptions\Models\SubscriptionProductSignupFlow;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel;
use Ntech\Subscriptions\Products\SignupFlow\SignupSettings;
use Ntech\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions_products_signup_flows")
 */
class SubscriptionProductSignupFlowModel
{
    /**
     * @var Uuid
     * @ORM\Column(name="id", type="guid")
    **/
    private $id;
    /**
     * @var SubscriptionProductModel
     * @ORM\ManyToOne(targetEntity="Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel")
     * @ORM\Id
     */
    private $subscriptionProduct;
    /**
     * @var string
     * @ORM\Column(name="flow_reference", type="string")
     * @ORM\Id
     */
    private $flowReference;
    /**
     * @var string
     * @ORM\Column(name="settings", type="json_array")
     */
    private $settings;

    public function __construct(
        Uuid $id,
        SubscriptionProductModel $subscriptionProduct,
        string $flowReference,
        SignupSettings $settings
    ) {
        $this->id = $id;
        $this->subscriptionProduct = $subscriptionProduct;
        $this->flowReference = $flowReference;
        $this->settings = $settings->serialize();
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return SubscriptionProductModel
     */
    public function getSubscriptionProduct()
    {
        return $this->subscriptionProduct;
    }

    /**
     * @return string
     */
    public function getFlowReference()
    {
        return $this->flowReference;
    }

    /**
     * @return SignupSettings
     */
    public function getSettings()
    {
        return SignupSettings::deserialize($this->settings);
    }

}
