<?php
namespace Ntech\Customers\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Payments\PaymentsServiceContainer;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\Cqrs\Query\QueryRegister;

class CustomersQueryRegister
{
    public function register(QueryRegister $queryRegister)
    {
        $entityManager = app(EntityManager::class);
        $queryProcessor = app(QueryProcessor::class);

        $queryRegister->addHandler(
            GetCustomerQuery::class,
            new GetCustomerHandler($entityManager)
        );
        $queryRegister->addHandler(
            GetCustomerInvoiceSummaryQuery::class,
            new GetCustomerInvoiceSummaryHandler(
                $entityManager
            )
        );
        $queryRegister->addHandler(
            GetCustomerByCompanyAndEmailQuery::class,
            new GetCustomerByCompanyAndEmailHandler(
                $entityManager
            )
        );
        $queryRegister->addHandler(
            GetCustomerContactQuery::class,
            new GetCustomerContactHandler(
                $entityManager
            )
        );
        $queryRegister->addHandler(
            GetCustomerAccountSelectionViewForCompanyQuery::class,
            new GetCustomerAccountSelectionViewForCompanyHandler(
                $entityManager
            )
        );
        $queryRegister->addHandler(
            GetCustomerAccountSelectionViewForUserQuery::class,
            new GetCustomerAccountSelectionViewForUserHandler(
                $entityManager
            )
        );
        $queryRegister->addHandler(
            GetCustomerDashboardPaymentSummaryQuery::class,
            new GetCustomerDashboardPaymentSummaryHandler(
                $entityManager,
                $queryProcessor,
                app(PaymentsServiceContainer::class)
            )
        );
        $queryRegister->addHandler(
            GetTotalActiveSubscriptionsForCustomerQuery::class,
            new GetTotalActiveSubscriptionsForCustomerHandler(
                $entityManager
            )
        );
        $queryRegister->addHandler(
            GetCustomerDashboardSubscriptionsSummaryQuery::class,
            new GetCustomerDashboardSubscriptionsSummaryHandler(
                $entityManager,
                $queryProcessor
            )
        );
    }
}
