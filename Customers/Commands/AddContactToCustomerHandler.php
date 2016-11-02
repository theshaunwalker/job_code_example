<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use Ntech\Customers\CustomerContact;
use Ntech\Customers\Events\ContactSetAsPrimary;
use Ntech\Customers\Events\CustomerCreated;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class AddContactToCustomerHandler extends CommandHandler
{

    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    public function __construct(EventSourcingRepositoryFactoryInterface $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function handle(AddContactToCustomerCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        /** @var Customer $customer */
        $customer = $customerRepo->load($command->getCustomerId());

        $customer->addContact($command->getCustomerContact());

        $customerRepo->save($customer);
    }
}
