<?php
namespace Ntech\Customers;

use Carbon\Carbon;
use Ntech\Support\ValueObjects\ValueObject;
use NtechUtility\Serializer\Serializable;

class CustomerBasicInfo extends ValueObject implements Serializable
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Carbon
     */
    private $customerSince;

    public function __construct(
        string $name,
        Carbon $customerSince
    ) {
        $this->name = $name;
        $this->customerSince = $customerSince;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new static(
            $data['name'],
            new Carbon($data['customerSince'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'name' => $this->name,
            'customerSince' => $this->customerSince->toDateTimeString()
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Carbon
     */
    public function getCustomerSince()
    {
        return $this->customerSince;
    }
}
