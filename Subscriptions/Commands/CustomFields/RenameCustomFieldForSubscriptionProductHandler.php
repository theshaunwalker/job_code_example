<?php
namespace Ntech\Subscriptions\Commands\CustomFields;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class RenameCustomFieldForSubscriptionProductHandler extends CommandHandler
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
    
    public function handle(RenameCustomFieldForSubscriptionProductCommand $command)
    {
        /** @var SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $this->subscriptionProductSourceRepo->load($command->getSubscriptionProductId());
        $subscriptionProduct->renameCustomField($command->getFieldSlug(), $command->getNewName());
        $this->subscriptionProductSourceRepo->save($subscriptionProduct);
    }
}
