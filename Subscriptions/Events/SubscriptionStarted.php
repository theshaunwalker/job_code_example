<?php
namespace Ntech\Subscriptions\Events;

use Carbon\Carbon;
use Ntech\Events\Event;
use Ntech\Subscriptions\SubscriptionPaymentDetails;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class SubscriptionStarted extends Event implements Serializable
{
    /**
     * @var Uuid
     */
    private $subscriptionId;
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
    private $name;
    /**
     * @var Carbon
     */
    private $startDate;
    /**
     * @var SubscriptionTerms
     */
    private $subscriptionTerms;

    public function __construct(
        Uuid $subscriptionId,
        Uuid $companyId,
        Uuid $customerId,
        string $name,
        Carbon $startDate,
        SubscriptionTerms $subscriptionTerms
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->companyId = $companyId;
        $this->customerId = $customerId;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->subscriptionTerms = $subscriptionTerms;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['subscriptionId']),
            Uuid::fromString($data['companyId']),
            Uuid::fromString($data['customerId']),
            $data['name'],
            new Carbon($data['startDate']),
            SubscriptionTerms::deserialize($data['subscriptionTerms'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'subscriptionId' => $this->subscriptionId->toString(),
            'companyId' => $this->companyId->toString(),
            'customerId' => $this->customerId->toString(),
            'name' => $this->name,
            'startDate' => $this->startDate->toDateTimeString(),
            'subscriptionTerms' => $this->subscriptionTerms->serialize()
        ];
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return SubscriptionTerms
     */
    public function getSubscriptionTerms()
    {
        return $this->subscriptionTerms;
    }

}
