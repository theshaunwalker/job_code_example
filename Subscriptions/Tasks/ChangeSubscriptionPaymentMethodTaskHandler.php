<?php
namespace Ntech\Subscriptions\Tasks;

use Ntech\CommandBus\CommandBus;
use Ntech\Companies\Queries\Payments\GetCompanyPaymentMethodQuery;
use Ntech\Payments\Commands\StartPaymentMethodSubscriptionCommand;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Subscriptions\Queries\GetSubscriptionQuery;
use Ntech\Uuid\UuidGenerator;
use NtechUtility\Cqrs\Query\QueryProcessor;

class ChangeSubscriptionPaymentMethodTaskHandler
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;
    /**
     * @var UuidGenerator
     */
    private $uuidGenerator;

    public function __construct(
        CommandBus $commandBus,
        QueryProcessor $queryProcessor,
        UuidGenerator $uuidGenerator
    ) {
        $this->commandBus = $commandBus;
        $this->queryProcessor = $queryProcessor;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function handle(ChangeSubscriptionPaymentMethodTask $task)
    {
        /** @var SubscriptionDoctrineModel $subscriptionModel */
        $subscriptionModel = $this->queryProcessor->process(
            new GetSubscriptionQuery($task->getSubscriptionId())
        );

        $companyPaymentMethod = $this->queryProcessor->process(
            new GetCompanyPaymentMethodQuery(
                $subscriptionModel->getCompanyId(),
                $task->getNewSubscribeableMethod()->getMethodKey()
            )
        );
        $companyPaymentMethod = $companyPaymentMethod->asCompanyPaymentMethodObject();


        $paymentSubscriptionId = $this->uuidGenerator->uuid4();

        $startPaymentSubscription = new StartPaymentMethodSubscriptionCommand(
            $paymentSubscriptionId,
            $task->getSubscriptionId(),
            $companyPaymentMethod,
            $task->getNewSubscribeableMethod()
        );
        $this->commandBus->handle($startPaymentSubscription);
    }
}
