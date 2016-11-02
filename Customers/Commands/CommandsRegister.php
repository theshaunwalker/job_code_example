<?php
namespace Ntech\Customers\Commands;

use Doctrine\ORM\EntityManager;
use Ntech\CommandBus\CommandsRegister as ParentRegister;
use Ntech\Companies\CompanyRepository;
use Ntech\Customers\Commands\CustomerUsers\AddUserToCustomerCommand;
use Ntech\Customers\Commands\CustomerUsers\AddUserToCustomerHandler;
use Ntech\Customers\Commands\CustomerUsers\RemoveUserFromCustomerCommand;
use Ntech\Customers\Commands\CustomerUsers\RemoveUserFromCustomerHandler;
use Ntech\Customers\Customer;
use Ntech\Customers\CustomerRepository;
use Ntech\Payments\Models\PaymentSingleDoctrine\SavedPaymentMethodDoctrineRepository;
use Ntech\Payments\PaymentsServiceContainer;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class CommandsRegister extends ParentRegister
{
    private $sourceFactory;

    public function __construct()
    {
        $this->sourceFactory = app(EventSourcingRepositoryFactoryInterface::class);
        parent::__construct();
    }
    public function loadCommands()
    {
        $this->customersCommands();
        $this->customerUserCommands();

//        $this->importCommands();
//        $this->tagCommands();
    }

    public function customersCommands()
    {
        $this->add(
            RegisterCustomerCommand::class,
            new RegisterCustomerHandler($this->sourceFactory)
        );
        $this->add(
            AddContactToCustomerCommand::class,
            new AddContactToCustomerHandler($this->sourceFactory)
        );
        $this->add(
            UpdateCustomerContactCommand::class,
            new UpdateCustomerContactHandler($this->sourceFactory)
        );
        $this->add(
            RemoveContactFromCustomerCommand::class,
            new RemoveContactFromCustomerHandler($this->sourceFactory)
        );
        $this->add(
            SetContactAsPrimaryCommand::class,
            new SetContactAsPrimaryHandler($this->sourceFactory)
        );
        $this->add(
            AddAddressToCustomerCommand::class,
            new AddAddressToCustomerHandler($this->sourceFactory)
        );
        $this->add(
            RemoveAddressFromCustomerCommand::class,
            new RemoveAddressFromCustomerHandler($this->sourceFactory)
        );
        $this->add(
            SetAddressAsPrimaryCommand::class,
            new SetAddressAsPrimaryHandler($this->sourceFactory)
        );
        $this->add(
            SetAddressAsBillingCommand::class,
            new SetAddressAsBillingHandler($this->sourceFactory)
        );
        $this->add(
            SetAddressAsShippingCommand::class,
            new SetAddressAsShippingHandler($this->sourceFactory)
        );

        $this->add(
            ApplyFreeCreditToCustomerCommand::class,
            new ApplyFreeCreditToCustomerHandler($this->sourceFactory)
        );
        
        $this->add(
            UpdateBasicInfoCommand::class,
            new UpdateBasicInfoHandler($this->sourceFactory)
        );
        $this->add(
            SetSubscriptionCardCommand::class,
            new SetSubscriptionCardHandler(
                $this->sourceFactory,
                app(SavedPaymentMethodDoctrineRepository::class),
                app(CustomerRepository::class),
                app(CompanyRepository::class),
                app(PaymentsServiceContainer::class)
            )
        );
    }

    public function customerUserCommands()
    {
        $this->add(
            AddUserToCustomerCommand::class,
            new AddUserToCustomerHandler($this->sourceFactory)
        );
        $this->add(
            RemoveUserFromCustomerCommand::class,
            new RemoveUserFromCustomerHandler($this->sourceFactory)
        );
    }
}
