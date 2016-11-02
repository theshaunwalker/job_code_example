<?php
namespace Ntech\Subscriptions\Models\Traits;

use Ntech\Subscriptions\Subscription;

trait ProvideSubscriptionStatusHtmlBadgeData
{
    abstract public function getBadgeDataStatus();

    public function getBadgeData()
    {
        switch ($this->getBadgeDataStatus()) {
            case Subscription::STATUS_ACTIVE:
                $badgeType = 'success';
                $badgeContent = 'Active';
                break;
            case Subscription::STATUS_SUSPENDED:
                $badgeType = 'warning';
                $badgeContent = 'Suspended';
                break;
            case Subscription::STATUS_CANCELLED:
                $badgeType = 'danger';
                $badgeContent = 'Cancelled';
                break;
        }
        return [
            'badgeType' => $badgeType,
            'badgeContent' => $badgeContent
        ];
    }
}
