<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class SetAddressAsShippingHandler extends CommandHandler
{
    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    public function __construct(EventSourcingRepositoryFactoryInterface $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function handle(SetAddressAsShippingCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        /** @var Customer $customer */
        $customer = $customerRepo->load($command->getCustomerId());
        
        $customer->setShippingAddress($command->getAddressId());
        
        $customerRepo->save($customer);
    }
}
