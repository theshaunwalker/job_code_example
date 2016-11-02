<?php
namespace Ntech\Subscriptions\Commands;

use Carbon\Carbon;
use Ntech\CommandBus\Command;
use Ntech\Subscriptions\SubscriptionPaymentDetails;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;

class StartSubscriptionCommand extends Command
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
    private $terms;

    public function __construct(
        Uuid $subscriptionId,
        Uuid $companyId,
        Uuid $customerId,
        string $name,
        Carbon $startDate,
        SubscriptionTerms $terms
    ) {
        $this->subscriptionId = $subscriptionId;
        $this->companyId = $companyId;
        $this->customerId = $customerId;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->terms = $terms;
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
    public function getTerms()
    {
        return $this->terms;
    }
}
