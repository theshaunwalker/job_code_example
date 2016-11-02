<?php
namespace Ntech\Customers\Exceptions;

use Ntech\Uuid\Uuid;

class CannotRemoveUserFromCustomer extends CustomerException
{
    public static function alreadyRemoved(Uuid $customerId, Uuid $userId)
    {
        return self::because("Cannot remove User [{$userId->toString()}] from Customer [{$customerId->toString()}]. They are not currently a user.");
    }
}
