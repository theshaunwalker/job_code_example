<?php
namespace Ntech\Customers\Exceptions;

use Ntech\Uuid\Uuid;

class CannotAddUserToCustomer extends CustomerException
{
    public static function becauseAlreadyAdded(Uuid $customerId, Uuid $userId)
    {
        return self::because(
            "Cannot add User [{$userId->toString()}] to Customer [{$customerId->toString()}]
            , because they are already added"
        );
    }
}
