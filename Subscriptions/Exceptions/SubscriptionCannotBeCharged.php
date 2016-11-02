<?php
namespace Ntech\Subscriptions\Exceptions;

use Ntech\Uuid\Uuid;

class SubscriptionCannotBeCharged extends SubscriptionException
{
    public static function prepayWhenNotPrepay(Uuid $subscriptionId)
    {
        return self::because("Cannot make a prepay charge subscription [{$subscriptionId->toString()}] as it is not a prepay subscription.");
    }
}
