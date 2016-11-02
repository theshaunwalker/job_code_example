<?php
namespace Ntech\Subscriptions\Commands\Products;

use Carbon\Carbon;
use Ntech\CommandBus\CommandHandler;
use Ntech\Subscriptions\Queries\Products\GetSubscriptionProductQuery;
use Ntech\Subscriptions\Subscription;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class StartSubscriptionFromProductHandler extends CommandHandler
{
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;
    /**
     * @var EventSourcingRepository
     */
    private $subscriptionSourceRepository;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory,
        QueryProcessor $queryProcessor
    ) {
        $this->queryProcessor = $queryProcessor;
        $this->subscriptionSourceRepository = $sourceFactory->forAggregate(Subscription::class);
    }

    public function handle(StartSubscriptionFromProductCommand $command)
    {
        $subscriptionProduct = $this->queryProcessor->process(
            new GetSubscriptionProductQuery($command->getSubscriptionProductId())
        );
        $tier = $subscriptionProduct->getTier($command->getTierId());
        $paymentOption = $tier->getPaymentOption($command->getTierPaymentOptionId());

        /** @var Subscription $subscription */
        $subscription = Subscription::started(
            $command->getSubscriptionId(),
            $subscriptionProduct->getCompanyId(),
            $command->getCustomerId(),
            $subscriptionProduct->getName() . ' (' . $tier->getInfo()->getName() . ')',
            Carbon::now(),
            $paymentOption->getTerms()
        );
        $subscription->isForSubscriptionProduct(
            $command->getSubscriptionProductId(),
            $command->getTierId(),
            $command->getTierPaymentOptionId()
        );

        $this->subscriptionSourceRepository->save($subscription);
    }
}
