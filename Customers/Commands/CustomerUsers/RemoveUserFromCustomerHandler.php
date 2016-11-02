<?php
namespace Ntech\Customers\Commands\CustomerUsers;

use Ntech\Customers\Customer;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class RemoveUserFromCustomerHandler
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

    public function handle(RemoveUserFromCustomerCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);

        /** @var Customer $customer */
        $customer = $customerRepo->load($command->getCustomerId());

        $customer->removeUser($command->getUserId());

        $customerRepo->save($customer);
    }
}
