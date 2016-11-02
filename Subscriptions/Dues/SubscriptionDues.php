<?php
namespace Ntech\Subscriptions\Dues;

use Ntech\Exceptions\DomainException;
use Ntech\Subscriptions\Events\SubscriptionDueGeneratedForPeriod;
use Ntech\Subscriptions\Events\SubscriptionDueInvoiceGenerated;
use Ntech\Subscriptions\Periods\SubscriptionPeriod;
use Ntech\Subscriptions\SubscriptionTerms;
use NtechUtility\EventSource\EventSourcedEntity;
use NtechUtility\EventSource\EventSourcedEntityTrait;

class SubscriptionDues implements EventSourcedEntity
{
    use EventSourcedEntityTrait;

    /**
     * @var SubscriptionDueCollection
     */
    private $dues;

    public function __construct(SubscriptionDueCollection $dues)
    {
        $this->dues = $dues;
    }

    /**
     * @return SubscriptionDueCollection
     */
    public function getDues()
    {
        return $this->dues;
    }

    public function applySubscriptionDueGeneratedForPeriod(SubscriptionDueGeneratedForPeriod $event)
    {
        $this->dues->put($event->getPeriodId(), $event->getDue());
    }
    
    public function applySubscriptionDueInvoiceGenerated(SubscriptionDueInvoiceGenerated $event)
    {
        $due = $this->dues->get($event->getPeriodId());
        $due->setInvoiceId($event->getInvoiceId());
    }

}
