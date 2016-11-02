<?php
namespace Ntech\Customers\Payments;

use NtechUtility\EventSource\EventSourcedEntity;
use NtechUtility\EventSource\EventSourcedEntityTrait;
use NtechUtility\Support\Collections\Collection;

class CustomerPayments implements EventSourcedEntity
{
    use EventSourcedEntityTrait;

    private $payments;
    
    public function __construct()
    {
        $this->payments = new Collection();
    }

}
