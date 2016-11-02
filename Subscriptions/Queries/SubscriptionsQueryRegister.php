<?php
namespace Ntech\Subscriptions\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Invoices\Models\SingleDoctrine\DoctrineInvoiceRepository;
use Ntech\Subscriptions\Queries\Products\FindCustomFieldsForSubscriptionProductHandler;
use Ntech\Subscriptions\Queries\Products\FindCustomFieldsForSubscriptionProductQuery;
use Ntech\Subscriptions\Queries\Products\FindSubscriptionProductsHandler;
use Ntech\Subscriptions\Queries\Products\FindSubscriptionProductsQuery;
use Ntech\Subscriptions\Queries\Products\FindSubscriptionProductTiersHandler;
use Ntech\Subscriptions\Queries\Products\FindSubscriptionProductTiersQuery;
use Ntech\Subscriptions\Queries\Products\GetSubscriptionProductHandler;
use Ntech\Subscriptions\Queries\Products\GetSubscriptionProductQuery;
use Ntech\Subscriptions\Queries\Products\GetSubscriptionProductSignupFlowHandler;
use Ntech\Subscriptions\Queries\Products\GetSubscriptionProductSignupFlowQuery;
use Ntech\Subscriptions\Queries\Products\IsCustomerSubscribedToProductHandler;
use Ntech\Subscriptions\Queries\Products\IsCustomerSubscribedToProductQuery;
use NtechUtility\Cqrs\Query\QueryRegister;

class SubscriptionsQueryRegister
{
    public function register(QueryRegister $register)
    {
        $entityManager = app(EntityManager::class);
        $register->addHandler(
            GetSubscriptionQuery::class,
            new GetSubscriptionHandler(
                $entityManager
            )
        );
        $register->addHandler(
            SubscriptionInvoicesQuery::class,
            new SubscriptionInvoicesQueryHandler(
                app(DoctrineInvoiceRepository::class)
            )
        );
        $register->addHandler(
            GetSubscriptionProductQuery::class,
            new GetSubscriptionProductHandler($entityManager)
        );
        $register->addHandler(
            FindSubscriptionProductsQuery::class,
            new FindSubscriptionProductsHandler($entityManager)
        );
        $register->addHandler(
            GetSubscriptionProductSignupFlowQuery::class,
            new GetSubscriptionProductSignupFlowHandler($entityManager)
        );
        $register->addHandler(
            FindSubscriptionProductTiersQuery::class,
            new FindSubscriptionProductTiersHandler($entityManager)
        );
        $register->addHandler(
            FindCustomFieldsForSubscriptionProductQuery::class,
            new FindCustomFieldsForSubscriptionProductHandler($entityManager)
        );
        $register->addHandler(
            IsCustomerSubscribedToProductQuery::class,
            new IsCustomerSubscribedToProductHandler($entityManager)
        );
        $register->addHandler(
            FindDueSubscriptionsForCustomerQuery::class,
            new FindDueSubscriptionsForCustomerHandler($entityManager)
        );
    }
}
