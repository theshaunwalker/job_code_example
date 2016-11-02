<?php
namespace Ntech\Customers\Events;

use Carbon\Carbon;
use Ntech\Customers\Customer;
use Ntech\Events\Event;
use NtechUtility\Serializer\Serializable;
use Ntech\Uuid\Uuid;

class CustomerRegistered extends Event implements Serializable
{

    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var string
     */
    private $customerName;
    /**
     * @var Carbon
     */
    private $registeredOn;
    /**
     * @var Carbon
     */
    private $customerSince;

    public function __construct(Uuid $customerId, Uuid $companyId, string $customerName, Carbon $registeredOn, Carbon $customerSince)
    {
        $this->customerId = $customerId;
        $this->companyId = $companyId;
        $this->customerName = $customerName;
        $this->registeredOn = $registeredOn;
        $this->customerSince = $customerSince;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return Uuid
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @return Carbon
     */
    public function getRegisteredOn()
    {
        return $this->registeredOn;
    }

    /**
     * @return Carbon
     */
    public function getCustomerSince()
    {
        return $this->customerSince;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['customerId']),
            Uuid::fromString($data['companyId']),
            $data['customerName'],
            new Carbon($data['registeredOn']),
            new Carbon($data['customerSince'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString(),
            'companyId' => $this->companyId->toString(),
            'customerName' => $this->customerName,
            'registeredOn' => $this->registeredOn->toDateTimeString(),
            'customerSince' => $this->customerSince->toDateTimeString()
        ];
    }
}
