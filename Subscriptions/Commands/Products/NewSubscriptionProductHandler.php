<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class NewSubscriptionProductHandler extends CommandHandler
{
    /**
     * @var EventSourcingRepository
     */
    private $subscriptionProductSourceRepo;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory
    ) {
        $this->subscriptionProductSourceRepo = $sourceFactory->forAggregate(SubscriptionProduct::class);
    }
    
    public function handle(NewSubscriptionProductCommand $command)
    {
        $newProduct = SubscriptionProduct::create(
            $command->getId(),
            $command->getCompanyId(),
            $command->getInfo()
        );
        $this->subscriptionProductSourceRepo->save($newProduct);
    }
}
