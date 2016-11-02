<?php
namespace Ntech\Subscriptions\Exceptions;

class SubscriptionCannotBeCancelled extends SubscriptionException
{
    public static function whenAlreadyCancelled()
    {
        return self::because("Subscription is already cancelled");
    }
}
