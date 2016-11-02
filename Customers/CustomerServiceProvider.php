<?php
namespace Ntech\Customers;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\ServiceProvider;
use Ntech\Companies\CompanyRepository;
use Ntech\Customers\Authorization\CustomerAuthorizer;
use Ntech\Customers\Authorization\LaravelCustomerAuthorizer;
use Ntech\Customers\Commands\CommandsRegister;
use Ntech\Customers\Models\CustomerContactDoctrine\CustomerContactDoctrineRepository;
use Ntech\Customers\Models\CustomersProjectorRegister;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineRepository;
use Ntech\Customers\Models\SingleDoctrine\SingleDoctrineProjector;
use Ntech\Customers\Queries\CustomersQueryRegister;
use Ntech\Customers\Tags\AssignedTag;
use Ntech\Customers\Tasks\CustomersTasksRegister;
use Ntech\Invoices\InvoiceRepository;
use Ntech\Invoices\Models\SingleDoctrine\DoctrineInvoiceRepository;
use Ntech\Payments\Models\GatewayCustomer\GatewayCustomerRepository;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineRepository;
use Ntech\Payments\PaymentsRepository;
use Ntech\Payments\PaymentsServiceContainer;
use Ntech\Subscriptions\SubscriptionsRepository;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\Cqrs\Query\QueryRegister;
use NtechUtility\EventSource\EventBus\EventBus;

class CustomerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(CustomerDoctrineRepository::class, function ($app) {
            return new CustomerDoctrineRepository(
                $app->make(EntityManager::class),
                $app->make(QueryProcessor::class)
            );
        });
        $this->app->bind(CustomerContactDoctrineRepository::class, function ($app) {
            return new CustomerContactDoctrineRepository(
                $app->make(EntityManager::class),
                $app->make(QueryProcessor::class)
            );
        });

        $this->app->bind(CustomerRepository::class, function ($app) {
            return new CustomerRepository(
                $app->make(EntityManager::class),
                $app->make(CustomerDoctrineRepository::class),
                $app->make(CustomerContactDoctrineRepository::class),
                $app->make(InvoiceRepository::class),
                $app->make(PaymentsRepository::class),
                $app->make(CompanyRepository::class),
                $app->make(SubscriptionsRepository::class),
                $app->make(GatewayCustomerRepository::class),
                $app->make(PaymentsServiceContainer::class)
            );
        });


        $this->app->bind(CustomerAuthorizer::class, function ($app) {
            return new LaravelCustomerAuthorizer(
                $app->make('session')->driver($app->make('session')->getDefaultDriver()),
                $app->make(QueryProcessor::class)
            );
        });

        $customersProjectors = (new CustomersProjectorRegister())
            ->loadProjectors(app())
            ->register(app(EventBus::class));

        $commands = (new CommandsRegister)->register(app('command.locator'));

        $tasks = (new CustomersTasksRegister(
            app(QueryProcessor::class),
            app(EntityManager::class),
            app('command.bus'),
            app('event.only.bus')
        ))->register(app('command.locator'));

        $queries = (new CustomersQueryRegister())->register(app(QueryRegister::class));

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
