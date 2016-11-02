<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class UpdateCustomersEmailHandler extends CommandHandler
{
    /**
     * @var EventSourcingRepository
     */
    private $customerSourceRepository;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory
    ) {
        $this->customerSourceRepository = $sourceFactory->forAggregate(Customer::class);
    }

    public function handle(UpdateCustomersEmailCommand $command)
    {
        $customer = $this->customerSourceRepository->load($command->getCustomerId());
        $customer->updateEmail($command->getNewEmail());
        $this->customerSourceRepository->save($customer);
    }
}
