<?php
namespace Ntech\Subscriptions;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\ServiceProvider;
use Ntech\Invoices\InvoiceRepository;
use Ntech\Payments\Models\PaymentSubscription\PaymentSubscriptionRepository;
use Ntech\Payments\PaymentsRepository;
use Ntech\Subscriptions\Tasks\SubscriptionTasksRegister;
use Ntech\Subscriptions\Commands\CommandsRegister;
use Ntech\Subscriptions\Events\Listeners\CreateDefaultSignupFlowOnSubscriptionProductionCreation;
use Ntech\Subscriptions\Events\Listeners\GenerateSubscriptionDues;
use Ntech\Subscriptions\Events\Listeners\ProcessSubscriptionPaymentAndApplyToInvoice;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineRepository;
use Ntech\Subscriptions\Models\Subscription\SubscriptionPeriodDoctrineRepository;
use Ntech\Subscriptions\Models\SubscriptionDues\SubscriptionDuesRepository;
use Ntech\Subscriptions\Models\SubscriptionProjectorRegister;
use Ntech\Subscriptions\Payments\SubscriptionPaymentHandler;
use Ntech\Subscriptions\Queries\SubscriptionsQueryRegister;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\Cqrs\Query\QueryRegister;
use NtechUtility\EventSource\EventBus\EventBus;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class SubscriptionsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(SubscriptionDoctrineRepository::class, function ($app) {
            return new SubscriptionDoctrineRepository(
                $app->make(EntityManager::class),
                $app->make(QueryProcessor::class)
            );
        });
        $this->app->bind(SubscriptionsRepository::class, function ($app) {
            return new SubscriptionsRepository(
                $app->make(SubscriptionDoctrineRepository::class),
                $app->make(SubscriptionDuesRepository::class),
                $app->make(InvoiceRepository::class),
                $app->make(PaymentSubscriptionRepository::class),
                $app->make(SubscriptionPeriodDoctrineRepository::class)
            );
        });

        $subscriptionProjectors = (new SubscriptionProjectorRegister())
            ->loadProjectors(app())
            ->register(app(EventBus::class));

        $commands = (new CommandsRegister)->register(app('command.locator'));

        $tasks = (new SubscriptionTasksRegister())->register(app('command.locator'));

        $queries = (new SubscriptionsQueryRegister())->register(app(QueryRegister::class));

        app(EventBus::class)->subscribe(
            new ProcessSubscriptionPaymentAndApplyToInvoice(
                app(SubscriptionPaymentHandler::class)
            )
        );

        app(EventBus::class)->subscribe(
            new GenerateSubscriptionDues(
                app(EventSourcingRepositoryFactoryInterface::class),
                app(SubscriptionsRepository::class),
                app('event.store.uuid.generator')
            )
        );
        app(EventBus::class)->subscribe(
            new CreateDefaultSignupFlowOnSubscriptionProductionCreation(
                app(EventSourcingRepositoryFactoryInterface::class),
                app('event.store.uuid.generator')
            )
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(SubscriptionPaymentHandler::class, function ($app) {
            return new SubscriptionPaymentHandler(
                $app->make(EventSourcingRepositoryFactoryInterface::class),
                $app->make(PaymentsRepository::class),
                $app->make(SubscriptionsRepository::class),
                $app->make(InvoiceRepository::class),
                $app->make('command.bus')
            );
        });
    }
}
