<?php
namespace Ntech\Subscriptions\Exceptions;

class SubscriptionCannotBeSuspended extends SubscriptionException
{
    public static function whenItsNotActive()
    {
        return self::because("Cannot suspend subscription as it is not active.");
    }
    
    public static function whenItsAlreadySuspended()
    {
        return self::because("Subscription is already suspended");
    }
}
