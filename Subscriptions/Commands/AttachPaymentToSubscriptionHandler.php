<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\CommandHandler;
use Ntech\Exceptions\DomainException;
use Ntech\Invoices\InvoiceRepository;
use Ntech\Payments\PaymentsRepository;
use Ntech\Payments\Queries\GetPaymentQuery;
use Ntech\Subscriptions\Subscription;
use Ntech\Subscriptions\SubscriptionsRepository;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class AttachPaymentToSubscriptionHandler extends CommandHandler
{
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;
    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    public function __construct(
        QueryProcessor $queryProcessor,
        EventSourcingRepositoryFactoryInterface $sourceFactory
    ) {
        $this->queryProcessor = $queryProcessor;
        $this->sourceFactory = $sourceFactory;
    }

    public function handle(AttachPaymentToSubscriptionCommand $command)
    {
        $payment = $this->queryProcessor->process(
            new GetPaymentQuery($command->getPaymentId())
        );

        if ($payment->isAssigned()) {
            throw DomainException::because("Cannot attach payment to subscription because it is already assigned to an entity");
        }

        $subscriptionRepo = $this->sourceFactory->forAggregate(Subscription::class);
        /** @var Subscription $subscription */
        $subscription = $subscriptionRepo->load($command->getSubscriptionId());
        $subscription->makePayment($command->getPaymentId());
        $subscriptionRepo->save($subscription);
    }
}
