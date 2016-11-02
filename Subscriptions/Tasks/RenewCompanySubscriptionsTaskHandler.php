<?php
namespace Ntech\Subscriptions\Tasks;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Ntech\CommandBus\CommandBus;
use Ntech\Subscriptions\Commands\RenewSubscriptionCommand;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Uuid\Uuid;

class RenewCompanySubscriptionsTaskHandler
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        CommandBus $commandBus,
        EntityManager $entityManager
    ) {
        $this->commandBus = $commandBus;
        $this->entityManager = $entityManager;
    }

    public function handle(RenewCompanySubscriptionsTask $task)
    {
        /**
         * TODO: when company config is properly implemented and companies can
         * configure how much in advance invoices are generated for things
         * modify this threshold to reference the company config instead of being hardcoded
         */

        $subscriptionRepo = $this->entityManager->getRepository(SubscriptionDoctrineModel::class);
        // Count number of subscriptions company has that are within due threshold
        $subscriptionCount = (int)$subscriptionRepo->createQueryBuilder('s')
            ->select('count(s.id)')
            ->where('s.company = :companyId')
            ->andWhere('s.renewalDate <= CURRENT_DATE()')
            ->setParameters([
                'companyId' => $task->getCompanyId()->toString()
            ])
            ->getQuery()
            ->getSingleScalarResult();

        $batch = 100;
        $cursor = 0;
        while ($cursor < $subscriptionCount) {
            $subscriptions = $subscriptionRepo->createQueryBuilder('s')
                ->select('s.id, s.renewalDate')
                ->where('s.company = :companyId')
                ->andWhere('s.renewalDate <= CURRENT_DATE()')
                ->setParameters([
                    'companyId' => $task->getCompanyId()->toString()
                ])
                ->setMaxResults($batch)
                ->setFirstResult($cursor)
                ->getQuery()
                ->getResult();
            foreach ($subscriptions as $subscription) {
                $this->commandBus->handle(
                    new RenewSubscriptionCommand(Uuid::fromString($subscription['id']))
                );
            }
            $cursor += $batch;
        }
    }
}
