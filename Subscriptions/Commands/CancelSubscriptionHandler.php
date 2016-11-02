<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Payments\Models\PaymentSubscription\PaymentSubscriptionRepository;
use Ntech\Payments\PaymentSubscription;
use Ntech\Subscriptions\Subscription;
use Ntech\Subscriptions\SubscriptionsRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class CancelSubscriptionHandler extends CommandHandler
{
    /**
     * @var EventSourcingRepository
     */
    private $subscriptionSourceRepository;
    /**
     * @var EventSourcingRepository
     */
    private $paymentSubscriptionSourceRepository;
    /**
     * @var SubscriptionsRepository
     */
    private $subscriptionsRepository;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory,
        SubscriptionsRepository $subscriptionsRepository
    ) {
        $this->subscriptionSourceRepository = $sourceFactory->forAggregate(Subscription::class);
        $this->paymentSubscriptionSourceRepository = $sourceFactory->forAggregate(PaymentSubscription::class);
        $this->subscriptionsRepository = $subscriptionsRepository;
    }
    public function handle(CancelSubscriptionCommand $command)
    {
        $subscription = $this->subscriptionSourceRepository->load($command->getSubscriptionId());

        $subscription->cancel($command->getReason());

        $this->subscriptionSourceRepository->save($subscription);
    }
}
