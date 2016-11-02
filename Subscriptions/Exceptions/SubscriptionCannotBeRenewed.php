<?php
namespace Ntech\Subscriptions\Exceptions;

class SubscriptionCannotBeRenewed extends SubscriptionException
{
    public static function whenNotActive()
    {
        return self::because("Subscription cannot be renewed because it is not active");
    }
}
