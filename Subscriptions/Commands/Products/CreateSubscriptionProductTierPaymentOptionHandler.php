<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class CreateSubscriptionProductTierPaymentOptionHandler extends CommandHandler
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
    
    public function handle(CreateSubscriptionProductTierPaymentOptionCommand $command)
    {
        /** @var SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $this->subscriptionProductSourceRepo->load($command->getSubscriptionProductId());

        $subscriptionProduct->addPaymentOptionForTier($command->getTierId(), $command->getPaymentOption());
        
        $this->subscriptionProductSourceRepo->save($subscriptionProduct);
    }
}
