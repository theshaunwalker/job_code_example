<?php
namespace Ntech\Customers\Commands;

use Doctrine\ORM\EntityManager;
use Ntech\CommandBus\CommandHandler;
use Ntech\Companies\CompanyRepository;
use Ntech\Customers\Customer;
use Ntech\Customers\CustomerRepository;
use Ntech\Exceptions\DomainException;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentMethodDoctrineRepository;
use Ntech\Payments\Models\PaymentSingleDoctrine\SavedPaymentMethodDoctrineRepository;
use Ntech\Payments\PaymentsServiceContainer;
use Ntech\Payments\Services\Stripe\StripeService;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class SetSubscriptionCardHandler extends CommandHandler
{
    private $customerSourceRepository;
    /**
     * @var SavedPaymentMethodDoctrineRepository
     */
    private $savedMethodRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var PaymentsServiceContainer
     */
    private $paymentsServiceContainer;
    
    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory,
        SavedPaymentMethodDoctrineRepository $savedMethodRepository,
        CustomerRepository $customerRepository,
        CompanyRepository $companyRepository,
        PaymentsServiceContainer $paymentsServiceContainer
    ) {
        $this->customerSourceRepository = $sourceFactory->forAggregate(Customer::class);
        $this->savedMethodRepository = $savedMethodRepository;
        $this->companyRepository = $companyRepository;
        $this->customerRepository = $customerRepository;
        $this->paymentsServiceContainer = $paymentsServiceContainer;
    }

    public function handle(SetSubscriptionCardCommand $command)
    {
        $savedMethod = $this->savedMethodRepository->get($command->getMethodId());
        if ($savedMethod->getMethodKey() != 'stripe') {
            throw DomainException::because("Trying to set subscription Stripe card when selected method is not a Stripe card. 
             (is a [{$savedMethod->getMethodKey()}]");
        }

        $customer = $this->customerRepository->getSingleCustomerModel($command->getCustomerId());
        $companyPaymentMethod = $this->companyRepository->getCompanyPaymentMethodForCompany($customer->getCompanyId(), 'stripe');

        $customer = $this->customerSourceRepository->load($command->getCustomerId());
        $customer->setSubscriptionCard($command->getMethodId());
        $this->customerSourceRepository->save($customer);

        $stripeService = $this->paymentsServiceContainer->getPaymentServiceForCompanyPaymentMethod($companyPaymentMethod);
        $stripeService->setSubscriptionCard($savedMethod->getReusableToken());
    }
}
