<?php
namespace Ntech\Customers\Payments;

use Ntech\Customers\Events\SavedPaymentMethodToCustomer;
use Ntech\Payments\Methods\PaymentMethodMetadata;
use Ntech\Payments\Methods\SavedPaymentMethodMetadata;
use Ntech\Payments\Processing\Gateways\ReusableToken;
use Ntech\Uuid\Uuid;
use NtechUtility\EventSource\EventSourcedAggregateRoot;
use NtechUtility\EventSource\EventSourcedAggregateRootTrait;

class CustomerSavedPaymentMethod
{
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
    private $methodKey;
    /**
     * @var ReusableToken
     */
    private $reusableToken;
    /**
     * @var PaymentMethodMetadata
     */
    private $metadata;
    /**
     * @var string
     */
    private $alias;

    public function __construct(
        Uuid $id,
        Uuid $companyId,
        Uuid $customerId,
        string $methodKey,
        ReusableToken $reusableToken,
        PaymentMethodMetadata $metadata
    ) {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->customerId = $customerId;
        $this->methodKey = $methodKey;
        $this->reusableToken = $reusableToken;
        $this->metadata = $metadata;
    }

    /**
     * @return Uuid
     */
    public function getSavedMethodId()
    {
        return $this->id;
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
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getMethodKey()
    {
        return $this->methodKey;
    }

    /**
     * @return ReusableToken
     */
    public function getReusableToken()
    {
        return $this->reusableToken;
    }

    /**
     * @return PaymentMethodMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
}
