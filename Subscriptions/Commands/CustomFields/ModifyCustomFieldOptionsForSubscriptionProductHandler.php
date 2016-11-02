<?php
namespace Ntech\Subscriptions\Commands\CustomFields;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class ModifyCustomFieldOptionsForSubscriptionProductHandler extends CommandHandler
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
    
    public function handle(ModifyCustomFieldOptionsForSubscriptionProductCommand $command)
    {
        /** @var SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $this->subscriptionProductSourceRepo->load($command->getSubscriptionProductId());
        if ($command->getNewOptions()->get('required', false)) {
            $subscriptionProduct->markCustomFieldAsRequired($command->getFieldSlug());
        } else {
            $subscriptionProduct->markCustomFieldAsNotRequired($command->getFieldSlug());
        }
        $subscriptionProduct->changeCustomFieldOptions($command->getFieldSlug(), $command->getNewOptions());
        $this->subscriptionProductSourceRepo->save($subscriptionProduct);
    }
}
