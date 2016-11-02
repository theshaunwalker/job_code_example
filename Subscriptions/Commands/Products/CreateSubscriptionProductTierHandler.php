<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class CreateSubscriptionProductTierHandler extends CommandHandler
{
    private $subscriptionProductSourceRepo;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory
    ) {
        $this->subscriptionProductSourceRepo = $sourceFactory->forAggregate(SubscriptionProduct::class);
    }

    public function handle(CreateSubscriptionProductTierCommand $command)
    {
        /** @var SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $this->subscriptionProductSourceRepo->load($command->getSubscriptionProductId());

        $subscriptionProduct->createTier($command->getTier());

        $this->subscriptionProductSourceRepo->save($subscriptionProduct);
    }
}
