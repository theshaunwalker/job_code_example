<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class SetContactAsPrimaryHandler extends CommandHandler
{
    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    public function __construct(EventSourcingRepositoryFactoryInterface $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function handle(SetContactAsPrimaryCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        $customer = $customerRepo->load($command->getCustomerId());

        $customer->setPrimaryContact($command->getContactId());

        $customerRepo->save($customer);
    }
}
