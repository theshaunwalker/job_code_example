<?php
namespace Ntech\Customers\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Customers\Models\Dashboard\SubscriptionsSummary\SubscriptionsSummaryListItemCollection;
use Ntech\Customers\Models\Dashboard\SubscriptionsSummary\SubscriptionsSummaryView;
use Ntech\Customers\Models\Dashboard\SubscriptionsSummary\SubscriptionSummaryListItem;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use NtechUtility\Cqrs\Query\QueryHandler;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\Support\Collections\Collection;

class GetCustomerDashboardSubscriptionsSummaryHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;

    public function __construct(
        EntityManager $entityManager,
        QueryProcessor $queryProcessor
    ) {
        $this->entityManager = $entityManager;
        $this->queryProcessor = $queryProcessor;
    }

    public function handle(GetCustomerDashboardSubscriptionsSummaryQuery $query)
    {
        $subscriptions = $this->entityManager->getRepository(SubscriptionDoctrineModel::class)
            ->createQueryBuilder('sub')
            ->where('sub.customer = :customer')
            ->setMaxResults(5)
            ->setParameters([
                'customer' => $query->getCustomerId()->toString()
            ])
            ->getQuery()
            ->getResult();
        $subscriptions = new Collection($subscriptions);
        $recentSubscriptionsListItems = new SubscriptionsSummaryListItemCollection();
        foreach ($subscriptions as $subscription) {
            $recentSubscriptionsListItems->push(
                new SubscriptionSummaryListItem($subscription)
            );
        }
        $activeCount = $this->queryProcessor->process(
            new GetTotalActiveSubscriptionsForCustomerQuery($query->getCustomerId())
        );
        return new SubscriptionsSummaryView($recentSubscriptionsListItems, $activeCount);
    }
}
