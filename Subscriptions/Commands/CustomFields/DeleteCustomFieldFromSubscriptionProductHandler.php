<?php
namespace Ntech\Subscriptions\Commands\CustomFields;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class DeleteCustomFieldFromSubscriptionProductHandler extends CommandHandler
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

    public function handle(DeleteCustomFieldFromSubscriptionProductCommand $command)
    {
        /** @var SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $this->subscriptionProductSourceRepo->load($command->getSubscriptionProductId());
        $subscriptionProduct->removeCustomField($command->getFieldSlug());
        $this->subscriptionProductSourceRepo->save($subscriptionProduct);
    }
}
