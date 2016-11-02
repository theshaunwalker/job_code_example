<?php
namespace Ntech\Customers\Models\CustomerAddresses\ListView;

use NtechUtility\Support\Collections\Collection;

class CustomerAddressesListView
{
    private $customerAddresses;
    
    public function __construct(array $customerAddresses)
    {
        $this->customerAddresses = new Collection($customerAddresses);
    }

    /**
     * @return Collection
     */
    public function getCustomerAddresses()
    {
        return $this->customerAddresses;
    }

    public function count()
    {
        return $this->customerAddresses->count();
    }
}
