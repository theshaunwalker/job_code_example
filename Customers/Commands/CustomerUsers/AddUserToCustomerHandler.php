<?php
namespace Ntech\Customers\Commands\CustomerUsers;

use Ntech\Customers\Customer;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class AddUserToCustomerHandler
{
    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory
    ) {
        $this->sourceFactory = $sourceFactory;
    }
    
    public function handle(AddUserToCustomerCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        /** @var Customer $customer */
        $customer = $customerRepo->load($command->getCustomerId());

        $customer->addUser($command->getUserId());

        $customerRepo->save($customer);
    }
}
