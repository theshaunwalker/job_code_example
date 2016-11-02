<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class UpdateBasicInfoHandler extends CommandHandler
{
    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    public function __construct(EventSourcingRepositoryFactoryInterface $sourceFactory)
    {

        $this->sourceFactory = $sourceFactory;
    }
    public function handle(UpdateBasicInfoCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        $customer = $customerRepo->load($command->getCustomerId());

        $customer->modifyBasicInfo($command->getCustomerBasicInfo());

        $customerRepo->save($customer);
    }
}
