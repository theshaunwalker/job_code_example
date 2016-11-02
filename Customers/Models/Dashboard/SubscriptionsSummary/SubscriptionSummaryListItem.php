<?php
namespace Ntech\Customers\Models\Dashboard\SubscriptionsSummary;

use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Subscriptions\Models\Traits\ProvideSubscriptionStatusHtmlBadgeData;

class SubscriptionSummaryListItem
{
    use ProvideSubscriptionStatusHtmlBadgeData;

    /**
     * @var SubscriptionDoctrineModel
     */
    private $subscription;

    public function __construct(
        SubscriptionDoctrineModel $subscription
    ) {
        $this->subscription = $subscription;
    }

    public function getBadgeDataStatus()
    {
        return $this->subscription->getStatus();
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->subscription, $method), $args);
    }
}
