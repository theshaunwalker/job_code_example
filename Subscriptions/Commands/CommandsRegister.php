<?php
namespace Ntech\Subscriptions\Commands;

use Ntech\CommandBus\CommandsRegister as ParentRegister;
use Ntech\Subscriptions\Commands\CustomFields\AddCustomFieldToSubscriptionProductCommand;
use Ntech\Subscriptions\Commands\CustomFields\AddCustomFieldToSubscriptionProductHandler;
use Ntech\Subscriptions\Commands\CustomFields\ModifyCustomFieldOptionsForSubscriptionProductCommand;
use Ntech\Subscriptions\Commands\CustomFields\ModifyCustomFieldOptionsForSubscriptionProductHandler;
use Ntech\Subscriptions\Commands\CustomFields\DeleteCustomFieldFromSubscriptionProductCommand;
use Ntech\Subscriptions\Commands\CustomFields\DeleteCustomFieldFromSubscriptionProductHandler;
use Ntech\Subscriptions\Commands\CustomFields\RenameCustomFieldForSubscriptionProductCommand;
use Ntech\Subscriptions\Commands\CustomFields\RenameCustomFieldForSubscriptionProductHandler;
use Ntech\Subscriptions\Commands\Products\CreateSubscriptionProductTierCommand;
use Ntech\Subscriptions\Commands\Products\CreateSubscriptionProductTierHandler;
use Ntech\Subscriptions\Commands\Products\CreateSubscriptionProductTierPaymentOptionCommand;
use Ntech\Subscriptions\Commands\Products\CreateSubscriptionProductTierPaymentOptionHandler;
use Ntech\Subscriptions\Commands\Products\RetireSubscriptionProductTierCommand;
use Ntech\Subscriptions\Commands\Products\RetireSubscriptionProductTierHandler;
use Ntech\Subscriptions\Commands\Products\DeleteSubscriptionProductTierPaymentOptionCommand;
use Ntech\Subscriptions\Commands\Products\DeleteSubscriptionProductTierPaymentOptionHandler;
use Ntech\Subscriptions\Commands\Products\NewSubscriptionProductCommand;
use Ntech\Subscriptions\Commands\Products\NewSubscriptionProductHandler;
use Ntech\Subscriptions\Commands\Products\StartSubscriptionFromProductCommand;
use Ntech\Subscriptions\Commands\Products\StartSubscriptionFromProductHandler;
use Ntech\Subscriptions\Commands\Products\UpdateSubscriptionProductInfoCommand;
use Ntech\Subscriptions\Commands\Products\UpdateSubscriptionProductInfoHandler;
use Ntech\Subscriptions\Commands\Products\UpdateSubscriptionProductTierInfoCommand;
use Ntech\Subscriptions\Commands\Products\UpdateSubscriptionProductTierInfoHandler;
use Ntech\Subscriptions\Commands\Products\UpdateSubscriptionProductTierPaymentOptionTermsCommand;
use Ntech\Subscriptions\Commands\Products\UpdateSubscriptionProductTierPaymentOptionTermsHandler;
use Ntech\Subscriptions\SubscriptionsRepository;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class CommandsRegister extends ParentRegister
{
    /**
     * @var EventSourcingRepositoryFactoryInterface
     */
    private $sourceFactory;

    /**
     * @var QueryProcessor
     */
    private $queryProcessor;

    public function loadCommands()
    {
        $this->sourceFactory = app(EventSourcingRepositoryFactoryInterface::class);
        $this->queryProcessor = app(QueryProcessor::class);
        $this->add(
            StartSubscriptionCommand::class,
            new StartSubscriptionHandler($this->sourceFactory)
        );
        $this->add(
            SuspendSubscriptionCommand::class,
            new SuspendSubscriptionHandler($this->sourceFactory)
        );
        $this->add(
            CancelSubscriptionCommand::class,
            new CancelSubscriptionHandler(
                $this->sourceFactory,
                app(SubscriptionsRepository::class)
            )
        );
        $this->add(
            ReactivateSubscriptionCommand::class,
            new ReactivateSubscriptionHandler($this->sourceFactory)
        );
        $this->add(
            RenewSubscriptionCommand::class,
            new RenewSubscriptionHandler($this->sourceFactory)
        );

        $this->subscriptionProductCommands();

    }

    public function subscriptionProductCommands()
    {
        $this->add(
            NewSubscriptionProductCommand::class,
            new NewSubscriptionProductHandler($this->sourceFactory)
        );
        $this->add(
            UpdateSubscriptionProductInfoCommand::class,
            new UpdateSubscriptionProductInfoHandler($this->sourceFactory)
        );
        $this->add(
            StartSubscriptionFromProductCommand::class,
            new StartSubscriptionFromProductHandler(
                $this->sourceFactory,
                $this->queryProcessor
            )
        );
        $this->add(
            CreateSubscriptionProductTierCommand::class,
            new CreateSubscriptionProductTierHandler($this->sourceFactory)
        );
        $this->add(
            UpdateSubscriptionProductTierInfoCommand::class,
            new UpdateSubscriptionProductTierInfoHandler($this->sourceFactory)
        );
        $this->add(
            RetireSubscriptionProductTierCommand::class,
            new RetireSubscriptionProductTierHandler($this->sourceFactory)
        );
        $this->add(
            CreateSubscriptionProductTierPaymentOptionCommand::class,
            new CreateSubscriptionProductTierPaymentOptionHandler($this->sourceFactory)
        );
        $this->add(
            DeleteSubscriptionProductTierPaymentOptionCommand::class,
            new DeleteSubscriptionProductTierPaymentOptionHandler($this->sourceFactory)
        );
        $this->add(
            UpdateSubscriptionProductTierPaymentOptionTermsCommand::class,
            new UpdateSubscriptionProductTierPaymentOptionTermsHandler($this->sourceFactory)
        );
        $this->add(
            AddCustomFieldToSubscriptionProductCommand::class,
            new AddCustomFieldToSubscriptionProductHandler($this->sourceFactory)
        );
        $this->add(
            RenameCustomFieldForSubscriptionProductCommand::class,
            new RenameCustomFieldForSubscriptionProductHandler($this->sourceFactory)
        );
        $this->add(
            ModifyCustomFieldOptionsForSubscriptionProductCommand::class,
            new ModifyCustomFieldOptionsForSubscriptionProductHandler($this->sourceFactory)
        );
        $this->add(
            DeleteCustomFieldFromSubscriptionProductCommand::class,
            new DeleteCustomFieldFromSubscriptionProductHandler($this->sourceFactory)
        );
    }
}
