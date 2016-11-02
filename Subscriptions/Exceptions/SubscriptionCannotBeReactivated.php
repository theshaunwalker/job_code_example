<?php
namespace Ntech\Subscriptions\Exceptions;

class SubscriptionCannotBeReactivated extends SubscriptionException
{
    public static function whenAlreadyCancelled()
    {
        return self::because("Cannot reactivate a cancelled subscription.");
    }

    public static function whenItsAlreadyActive()
    {
        return self::because("Cannot reactivate subscription as it is already active.");
    }
}
