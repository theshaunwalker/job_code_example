<?php
namespace Ntech\Customers\Models\Customer\ListView;

use Ntech\Customers\Models\SingleDoctrine\CustomerCollection;

class CustomerListView
{
    /**
     * @var CustomerCollection
     */
    private $customers;

    public function __construct(CustomerCollection $customers)
    {
        $this->customers = $customers;
    }

    /**
     * @return CustomerCollection
     */
    public function getCustomers()
    {
        return $this->customers;
    }

}
