<?php
namespace Ntech\Customers\Models\CustomerContacts\ListView;

use NtechUtility\Support\Collections\Collection;

class CustomerContactsList
{
    /**
     * @var array
     */
    private $contacts;

    public function __construct(array $contacts)
    {
        $this->contacts = new Collection($contacts);
    }
    
    public function getContacts()
    {
        return $this->contacts;
    }
}
