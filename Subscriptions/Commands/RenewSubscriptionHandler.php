<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Subscription;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class RenewSubscriptionHandler extends CommandHandler
{
    /**
     * @var EventSourcingRepository
     */
    private $subscriptionSourceRepository;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory
    ) {
        $this->subscriptionSourceRepository = $sourceFactory->forAggregate(Subscription::class);
    }
    
    public function handle(RenewSubscriptionCommand $command)
    {
        /*
         * Cycle the subscription periods
         */
        $subscription = $this->subscriptionSourceRepository->load($command->getSubscriptionId());
        $subscription->renew();
        $this->subscriptionSourceRepository->save($subscription);
        /**
         * Event listeners will generate the relevent subscription dues
         */
    }
}
