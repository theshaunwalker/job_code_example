<?php
namespace Ntech\Customers\Commands\Tags;

use Ntech\CommandBus\Command;
use Ntech\Customers\Customer;
use Ntech\Customers\Tags\Tag;

class AddTagToCustomerCommand extends Command
{

    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var Tag
     */
    protected $tagName;

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
