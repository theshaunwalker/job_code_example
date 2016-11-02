<?php
namespace Ntech\Customers\Events;

use Ntech\Events\Event;
use Ntech\Payments\Methods\PaymentMethodMetadata;
use Ntech\Payments\Processing\Gateways\ReusableToken;
use Ntech\Payments\Processing\Gateways\TokenSerializer;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SavedPaymentMethodToCustomer extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $savedMethodId;
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
    private $paymentMethodKey;
    /**
     * @var ReusableToken
     */
    private $reusableToken;
    /**
     * @var array
     */
    private $metadata;
    /**
     * @var bool
     */
    private $subscribeable;

    public function __construct(
        Uuid $savedMethodId,
        Uuid $companyId,
        Uuid $customerId,
        string $paymentMethodKey,
        ReusableToken $reusableToken,
        PaymentMethodMetadata $metadata,
        bool $subscribeable
    ) {
        $this->savedMethodId = $savedMethodId;
        $this->companyId = $companyId;
        $this->customerId = $customerId;
        $this->paymentMethodKey = $paymentMethodKey;
        $this->reusableToken = $reusableToken;
        $this->metadata = $metadata;
        $this->subscribeable = $subscribeable;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['savedMethodId']),
            Uuid::fromString($data['companyId']),
            Uuid::fromString($data['customerId']),
            $data['paymentMethodKey'],
            TokenSerializer::deserialize($data['reusableToken']),
            isset($data['metadata']) ?
                PaymentMethodMetadata::deserialize($data['metadata']) :
                PaymentMethodMetadata::deserialize(['metadata' => []]),
            $data['subscribeable']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'savedMethodId' => $this->savedMethodId->toString(),
            'companyId' => $this->companyId->toString(),
            'customerId' => $this->customerId->toString(),
            'paymentMethodKey' => $this->paymentMethodKey,
            'reusableToken' => TokenSerializer::serialize($this->reusableToken),
            'metadata' => $this->metadata->serialize(),
            'subscribeable' => $this->subscribeable
        ];
    }

    /**
     * @return Uuid
     */
    public function getSavedMethodId()
    {
        return $this->savedMethodId;
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
    public function getPaymentMethodKey()
    {
        return $this->paymentMethodKey;
    }

    /**
     * @return ReusableToken
     */
    public function getToken()
    {
        return $this->reusableToken;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return boolean
     */
    public function isSubscribeable()
    {
        return $this->subscribeable;
    }
}
