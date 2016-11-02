<?php
namespace Ntech\Customers\Tasks;

use Doctrine\ORM\EntityManager;
use Ntech\CommandBus\CommandBus;
use Ntech\CommandBus\CommandsRegister;
use Ntech\EmailTemplates\EmailTemplateCompiler;
use Ntech\Events\EventBus;
use Ntech\Uuid\Uuid;
use Ntech\Uuid\UuidGenerator;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class CustomersTasksRegister extends CommandsRegister
{
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var EventBus
     */
    private $eventBus;

    public function __construct(
        QueryProcessor $queryProcessor,
        EntityManager $entityManager,
        CommandBus $commandBus,
        EventBus $eventBus
    ) {
        $this->queryProcessor = $queryProcessor;
        $this->entityManager = $entityManager;
        $this->commandBus = $commandBus;
        $this->eventBus = $eventBus;
        parent::__construct();
    }

    public function loadCommands()
    {
        $container = app();
        $this->add(
            SendDueItemsSummaryReminderToCustomerTask::class,
            new SendDueItemsSummaryReminderToCustomerHandler(
                $this->queryProcessor,
                $container->make(EmailTemplateCompiler::class),
                $this->commandBus,
                $this->eventBus
            )
        );
        $this->add(
            SendSummaryRemindersToAllCustomersTask::class,
            new SendSummaryRemindersToAllCustomersHandler(
                $this->entityManager,
                $this->commandBus
            )
        );
    }
}
