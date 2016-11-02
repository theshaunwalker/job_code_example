<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use Ntech\Customers\Events\CustomerCreated;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class RegisterCustomerHandler extends CommandHandler
{

    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    public function __construct(EventSourcingRepositoryFactoryInterface $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function handle(RegisterCustomerCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        $customer = Customer::register(
            $command->getCustomerId(),
            $command->getCompanyId(),
            $command->getCustomerName(),
            $command->getCustomerSince()
        );
        if ($command->getEmail() != null) {
            $customer->updateEmail($command->getEmail());
        }
        $customerRepo->save($customer);
    }
}
