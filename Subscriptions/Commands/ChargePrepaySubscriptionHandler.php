<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Exceptions\SubscriptionCannotBeCharged;
use Ntech\Subscriptions\Subscription;
use Ntech\Subscriptions\SubscriptionsRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class ChargePrepaySubscriptionHandler extends CommandHandler
{
    /**
     * @var SubscriptionsRepository
     */
    private $subscriptionsRepository;

    /**
     * @var EventSourcingRepository
     */
    private $subscriptionSourceRepository;

    public function __construct(
        SubscriptionsRepository $subscriptionsRepository,
        EventSourcingRepositoryFactoryInterface $sourceFactory
    ) {
        $this->subscriptionsRepository = $subscriptionsRepository;
        $this->subscriptionSourceRepository = $sourceFactory->forAggregate(Subscription::class);
    }

    public function handle(ChargePrepaySubscriptionCommand $command)
    {
        $subscriptionModel = $this->subscriptionsRepository->getSubscription($command->getSubscriptionId());

        if (!$subscriptionModel->isPrepay()) {
            throw SubscriptionCannotBeCharged::prepayWhenNotPrepay($command->getSubscriptionId());
        }

        
    }
}
