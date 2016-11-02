<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;

class UpdateSubscriptionProductTierPaymentOptionTermsCommand extends Command
{
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Uuid
     */
    private $tierId;
    /**
     * @var Uuid
     */
    private $paymentOptionId;
    /**
     * @var SubscriptionTerms
     */
    private $terms;

    public function __construct(
        Uuid $companyId,
        Uuid $subscriptionProductId,
        Uuid $tierId,
        Uuid $paymentOptionId,
        SubscriptionTerms $terms
    ) {
        $this->companyId = $companyId;
        $this->subscriptionProductId = $subscriptionProductId;
        $this->tierId = $tierId;
        $this->paymentOptionId = $paymentOptionId;
        $this->terms = $terms;
    }

    /**
     * @return Uuid
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return Uuid
     */
    public function getTierId()
    {
        return $this->tierId;
    }

    /**
     * @return Uuid
     */
    public function getPaymentOptionId()
    {
        return $this->paymentOptionId;
    }

    /**
     * @return SubscriptionTerms
     */
    public function getTerms()
    {
        return $this->terms;
    }
}
