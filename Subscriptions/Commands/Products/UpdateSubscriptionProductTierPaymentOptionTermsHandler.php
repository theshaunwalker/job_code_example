<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class UpdateSubscriptionProductTierPaymentOptionTermsHandler extends CommandHandler
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
    
    public function handle(UpdateSubscriptionProductTierPaymentOptionTermsCommand $command)
    {
        /** @var SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $this->subscriptionProductSourceRepo->load($command->getSubscriptionProductId());

        $subscriptionProduct->updatePaymentOptionTermsForTier(
            $command->getTierId(),
            $command->getPaymentOptionId(),
            $command->getTerms()
        );
        
        $this->subscriptionProductSourceRepo->save($subscriptionProduct);
    }
}
