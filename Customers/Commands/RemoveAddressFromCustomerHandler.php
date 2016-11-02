<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class RemoveAddressFromCustomerHandler extends CommandHandler
{
    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    public function __construct(EventSourcingRepositoryFactoryInterface $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }
    public function handle(RemoveAddressFromCustomerCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        /** @var Customer $customer */
        $customer = $customerRepo->load($command->getCustomerId());

        $customer->removeAddress($command->getAddressId());

        $customerRepo->save($customer);
    }
}
