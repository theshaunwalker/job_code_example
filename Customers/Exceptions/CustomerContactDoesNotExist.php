<?php
namespace Ntech\Customers\Exceptions;

use Ntech\Uuid\Uuid;

class CustomerContactDoesNotExist extends CustomerException
{
    public static function forType(Uuid $customerId, integer $customerType)
    {
        return self::because("Customer [{$customerId->toString()}] does not have contact of type [{$customerType}]");
    }
}
