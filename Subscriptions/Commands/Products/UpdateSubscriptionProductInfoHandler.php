<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class UpdateSubscriptionProductInfoHandler extends CommandHandler
{
    private $subscriptionProductSourceRepository;
    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory
    ) {
        $this->subscriptionProductSourceRepository = $sourceFactory->forAggregate(SubscriptionProduct::class);
    }
    
    public function handle(UpdateSubscriptionProductInfoCommand $command)
    {
        $subProduct = $this->subscriptionProductSourceRepository->load($command->getSubProductId());
        $subProduct->updateInfo($command->getSubProductInfo());
        $this->subscriptionProductSourceRepository->save($subProduct);
    }
}
