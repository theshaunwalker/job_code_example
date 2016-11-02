<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Subscription;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class ReactivateSubscriptionHandler extends CommandHandler
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

    public function handle(ReactivateSubscriptionCommand $command)
    {
        $subscription = $this->subscriptionSourceRepository->load($command->getSubscriptionId());

        $subscription->reactivate();

        $this->subscriptionSourceRepository->save($subscription);
    }
}
