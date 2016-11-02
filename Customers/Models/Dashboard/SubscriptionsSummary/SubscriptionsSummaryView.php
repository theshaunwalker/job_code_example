<?php
namespace Ntech\Customers\Models\Dashboard\SubscriptionsSummary;

use NtechUtility\Support\Collections\Collection;

class SubscriptionsSummaryView
{
    /**
     * @var SubscriptionsSummaryListItemCollection
     */
    private $recentSubscriptions;
    /**
     * @var int
     */
    private $activeCount;

    public function __construct(
        SubscriptionsSummaryListItemCollection $recentSubscriptions,
        int $activeCount
    ) {
        $this->recentSubscriptions = $recentSubscriptions;
        $this->activeCount = $activeCount;
    }

    /**
     * @return SubscriptionsSummaryListItemCollection
     */
    public function getRecentSubscriptions()
    {
        return $this->recentSubscriptions;
    }
    
    public function getActiveCount()
    {
        return $this->activeCount;
    }
}
