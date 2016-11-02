<?php
namespace Ntech\Customers\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Customer;
use Ntech\Customers\CustomerAddresses;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class AddAddressToCustomerHandler extends CommandHandler
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

    public function handle(AddAddressToCustomerCommand $command)
    {
        $customerRepo = $this->sourceFactory->forAggregate(Customer::class);
        /** @var Customer $customer */
        $customer = $customerRepo->load($command->getCustomerId());

        $customer->addAddress(
            $command->getAddress(),
            $command->getAddressDefaultsFor()
        );
        $addressId = $command->getAddress()->getAddressId();
        foreach ($command->getAddressDefaultsFor() as $default) {
            switch ($default) {
                case CustomerAddresses::DEFAULT_ADDRESS_FOR_EVERYTHING:
                    $customer->setPrimaryAddress($addressId);
                    $customer->setShippingAddress($addressId);
                    $customer->setBillingAddress($addressId);
                    break;
                case CustomerAddresses::PRIMARY_ADDRESS:
                    $customer->setPrimaryAddress($addressId);
                    break;
                case CustomerAddresses::SHIPPING_ADDRESS:
                    $customer->setShippingAddress($addressId);
                    break;
                case CustomerAddresses::BILLING_ADDRESS:
                    $customer->setBillingAddress($addressId);
                    break;
            }
        }

        $customerRepo->save($customer);
    }
}
