<?php
namespace Ntech\Subscriptions\Periods;

use Carbon\Carbon;
use Ntech\Subscriptions\Events\SubscriptionPeriodEnded;
use Ntech\Subscriptions\Events\SubscriptionPeriodStarted;
use Ntech\Subscriptions\Exceptions\SubscriptionException;
use Ntech\Subscriptions\SubscriptionTerms;
use Ntech\Uuid\Uuid;
use NtechUtility\EventSource\EventSourcedEntity;
use NtechUtility\EventSource\EventSourcedEntityTrait;

class SubscriptionPeriods implements EventSourcedEntity
{
    use EventSourcedEntityTrait;

    /**
     * @var SubscriptionPeriodCollection
     */
    private $periods;
    /**
     * The currently active subscription period
     * @var SubscriptionPeriod
     */
    private $current;

    public function __construct()
    {
        $this->periods = new SubscriptionPeriodCollection();
    }

    public function applySubscriptionPeriodEnded(SubscriptionPeriodEnded $event)
    {
        $this->current = null;
    }

    public function applySubscriptionPeriodStarted(SubscriptionPeriodStarted $event)
    {
        $this->periods->put($event->getPeriod()->getOrderCount(), $event->getPeriod());
        $this->current = $event->getPeriod();
    }
    
    public function cycleToNextPeriod(SubscriptionTerms $terms)
    {
        $lastPeriod = $this->periods->last();

        $nextPeriod = $this->generateNextPeriod($terms);
        $this->apply(
            new SubscriptionPeriodEnded(
                $this->getAggregateRootId(),
                $lastPeriod
            )
        );
        $this->apply(
            new SubscriptionPeriodStarted(
                $this->getAggregateRootId(),
                $nextPeriod
            )
        );
    }

    /**
     * Creates a new SubscriptionPeriod which begins at the
     * end of the current one, and has an end date calculated form the passed terms.
     *
     * @param SubscriptionTerms $terms
     * @return SubscriptionPeriod
     */
    public function generateNextPeriod(
        SubscriptionTerms $terms
    ) {
        if ($this->current == null) {
            throw SubscriptionException::because("There is no current subscription period. Are you sure this subscription active?");
        }
        $nextPeriod = $this->current->nextPeriod($terms);
        return $nextPeriod;
    }

}
