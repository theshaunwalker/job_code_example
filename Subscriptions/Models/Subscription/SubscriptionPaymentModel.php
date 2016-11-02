<?php
namespace Ntech\Subscriptions\Models\Subscription;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions_payments")
 */
class SubscriptionPaymentModel
{
    /**
     * @var Uuid
     * @ORM\Column(name="subscription_id", type="guid")
     * @ORM\Id
     **/
    private $subscriptionId;
    /**
     * @var Uuid
     * @ORM\Column(name="payment_id", type="guid")
     * @ORM\Id
    **/
    private $paymentId;

    public function __construct(
        Uuid $subscriptionId,
        Uuid $paymentId
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->paymentId = $paymentId;
    }

    /**
     * @return Uuid
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }
}
