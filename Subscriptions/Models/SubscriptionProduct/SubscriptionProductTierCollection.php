<?php
namespace Ntech\Subscriptions\Models\SubscriptionProduct;

use NtechUtility\Support\Collections\Collection;

class SubscriptionProductTierCollection extends Collection
{
    protected $itemType = SubscriptionProductTierModel::class;

    public function onlyNotRetired()
    {
        return $this->filter(function ($tier) {
            return !$tier->isRetired();
        });
    }
    
    public function onlyRetired()
    {
        return $this->filter(function ($tier) {
            return $tier->isRetired();
        });
    }
}
