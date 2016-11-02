<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class ApplyFreeCreditToCustomerHandler extends CommandHandler
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
    public function handle(ApplyFreeCreditToCustomerCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        $customer = $customerRepo->load($command->getCustomerId());

        $customer->addCredit($command->getAmount());

        $customerRepo->save($customer);
    }
}
