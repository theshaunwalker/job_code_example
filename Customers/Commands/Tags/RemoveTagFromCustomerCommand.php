<?php
namespace Ntech\Customers\Commands\Tags;

use Carbon\Carbon;
use Ntech\CommandBus\Command;
use Ntech\Customers\Customer;
use Ntech\Customers\Tags\Tag;

class RemoveTagFromCustomerCommand extends Command
{

    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var Tag
     */
    protected $tag;

    public function __construct(Customer $customer, Tag $tag)
    {
        $this->customer = $customer;
        $this->tag = $tag;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

}
