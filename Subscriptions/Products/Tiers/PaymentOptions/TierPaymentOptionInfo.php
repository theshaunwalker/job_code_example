<?php
namespace Ntech\Subscriptions\Products\Tiers\PaymentOptions;

use NtechUtility\Serializer\Serializable;

class TierPaymentOptionInfo implements Serializable
{
    /**
     * @var string
     */
    private $reference;

    public function __construct(
        string $reference = ''
    ) {
        $this->reference = $reference;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            $data['reference']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'reference' => $this->reference
        ];
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
