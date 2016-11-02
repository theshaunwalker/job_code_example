<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Subscription;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class StartSubscriptionHandler extends CommandHandler
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

    public function handle(StartSubscriptionCommand $command)
    {
        $subscription = Subscription::started(
            $command->getSubscriptionId(),
            $command->getCompanyId(),
            $command->getCustomerId(),
            $command->getName(),
            $command->getStartDate(),
            $command->getTerms()
        );

        $this->subscriptionSourceRepository->save($subscription);
    }
}
