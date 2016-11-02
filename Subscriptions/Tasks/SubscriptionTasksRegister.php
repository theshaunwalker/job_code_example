<?php
namespace Ntech\Subscriptions\Tasks;

use Doctrine\ORM\EntityManager;
use Ntech\CommandBus\CommandBus;
use Ntech\Payments\Processing\Webhooks\PaymentServiceLocator;
use Ntech\Uuid\UuidGenerator;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\Tasks\TasksRegister;

class SubscriptionTasksRegister extends TasksRegister
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;
    /**
     * @var UuidGenerator
     */
    private $eventStoreUuidGenerator;

    public function loadTasks()
    {
        $this->commandBus = app('command.bus');
        $this->entityManager = app(EntityManager::class);
        $this->queryProcessor = app(QueryProcessor::class);
        $this->eventStoreUuidGenerator = app('event.store.uuid.generator');

        $this->add(
            RenewCompanySubscriptionsTask::class,
            new RenewCompanySubscriptionsTaskHandler(
                $this->commandBus,
                $this->entityManager
            )
        );
        $this->add(
            ChangeSubscriptionPaymentMethodTask::class,
            new ChangeSubscriptionPaymentMethodTaskHandler(
                $this->commandBus,
                $this->queryProcessor,
                $this->eventStoreUuidGenerator
            )
        );
        $this->add(
            PayOutstandingSubscriptionInvoicesTask::class,
            new PayOutstandingSubscriptionInvoicesTaskHandler(
                $this->commandBus,
                $this->queryProcessor,
                $this->entityManager,
                app(PaymentServiceLocator::class),
                $this->eventStoreUuidGenerator
            )
        );
    }
}
