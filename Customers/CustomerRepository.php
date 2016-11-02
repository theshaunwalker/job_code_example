<?php
namespace Ntech\Customers;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Ntech\Companies\CompanyRepository;
use Ntech\Companies\Models\CompanyPaymentMethods\CompanyPaymentMethodCollection;
use Ntech\Companies\Models\CompanyPaymentMethods\ListView\CompanyPaymentMethodsListView;
use Ntech\Customers\Models\CustomerAddresses\CustomerAddress;
use Ntech\Customers\Models\CustomerAddresses\ListView\CustomerAddressesListView;
use Ntech\Customers\Models\CustomerContactDoctrine\CustomerContactDoctrineRepository;
use Ntech\Customers\Models\CustomerContacts\ListView\CustomerContactListItem;
use Ntech\Customers\Models\CustomerContacts\ListView\CustomerContactsList;
use Ntech\Customers\Models\CustomerCreditBalance\CustomerCreditBalanceView;
use Ntech\Customers\Models\Customer\ListView\CustomerListView;
use Ntech\Customers\Models\Dashboard\SubscriptionsSummary\SubscriptionsSummaryListItemCollection;
use Ntech\Customers\Models\Dashboard\SubscriptionsSummary\SubscriptionsSummaryView;
use Ntech\Customers\Models\Dashboard\SubscriptionsSummary\SubscriptionSummaryListItem;
use Ntech\Customers\Models\InvoicesSummaryForDashboard\InvoicesSummaryForDashboardView;
use Ntech\Customers\Models\PaymentsSummaryForDashboard\PaymentsSummaryForDashboardView;
use Ntech\Customers\Models\PaymentsSummaryForDashboard\SinglePaymentSummary;
use Ntech\Customers\Models\SingleDoctrine\CustomerCollection;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineRepository;
use Ntech\Invoices\InvoiceRepository;
use Ntech\Payments\Models\GatewayCustomer\GatewayCustomerModel;
use Ntech\Payments\Models\GatewayCustomer\GatewayCustomerRepository;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineRepository;
use Ntech\Payments\Models\SavedPaymentMethod\ListView\SavedPaymentMethodListItem;
use Ntech\Payments\Models\SavedPaymentMethod\ListView\SavedPaymentMethodListView;
use Ntech\Payments\PaymentsRepository;
use Ntech\Payments\PaymentsServiceContainer;
use Ntech\Subscriptions\SubscriptionsRepository;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\PaginateCriteria;
use NtechUtility\Money\Amount;
use NtechUtility\Support\Collections\Collection;

class CustomerRepository
{
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var CustomerDoctrineRepository
     */
    private $customerDoctrineRepository;
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;
    /**
     * @var CustomerContactDoctrineRepository
     */
    private $customerContactDoctrineRepository;
    /**
     * @var PaymentDoctrineRepository
     */
    private $paymentsRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var SubscriptionsRepository
     */
    private $subscriptionsRepository;
    /**
     * @var GatewayCustomerRepository
     */
    private $gatewayCustomerRepository;
    /**
     * @var PaymentsServiceContainer
     */
    private $paymentsServiceContainer;

    public function __construct(
        EntityManager $entityManager,
        CustomerDoctrineRepository $customerDoctrineRepository,
        CustomerContactDoctrineRepository $customerContactDoctrineRepository,
        InvoiceRepository $invoiceRepository,
        PaymentsRepository $paymentsRepository,
        CompanyRepository $companyRepository,
        SubscriptionsRepository $subscriptionsRepository,
        GatewayCustomerRepository $gatewayCustomerRepository,
        PaymentsServiceContainer $paymentsServiceContainer
    ) {
        $this->entityManager = $entityManager;
        $this->customerDoctrineRepository = $customerDoctrineRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->customerContactDoctrineRepository = $customerContactDoctrineRepository;
        $this->paymentsRepository = $paymentsRepository;
        $this->companyRepository = $companyRepository;
        $this->subscriptionsRepository = $subscriptionsRepository;
        $this->gatewayCustomerRepository = $gatewayCustomerRepository;
        $this->paymentsServiceContainer = $paymentsServiceContainer;
    }

    public function forCompany(Uuid $companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }

    public function getSingleCustomerModel(Uuid $customerId)
    {
        if ($this->companyId != null) {
            $this->customerDoctrineRepository->forCompany($this->companyId);
        }
        return $this->customerDoctrineRepository->get($customerId->toString());
    }

    public function getCustomerListView()
    {
        if ($this->companyId != null) {
            $this->customerDoctrineRepository->forCompany($this->companyId);
        }
        $results = $this->customerDoctrineRepository->paginate(1, 20)->retrieve();

        $customers = new CustomerCollection($results);

        return new CustomerListView($customers);
    }

    public function getInvoicesSummaryForDashboard(Uuid $customerId, Carbon $windowStartDate) : InvoicesSummaryForDashboardView
    {
        if ($this->companyId != null) {
            $this->customerDoctrineRepository->forCompany($this->companyId);
        }
        $customer = $this->customerDoctrineRepository->get($customerId->toString());

        $paginateCriteria = new PaginateCriteria(1, 30, 'generatedOn');
        $this->invoiceRepository->
        $invoices = $this->invoiceRepository->getInvoicesForCustomer($customerId, $paginateCriteria);

        return new InvoicesSummaryForDashboardView($invoices, $total);
    }

    public function getCustomerCreditBalance(Uuid $customerId) : CustomerCreditBalanceView
    {
        return CustomerCreditBalanceView::fromDoctrine($this->entityManager, $customerId);
    }

    public function getCustomerAddressesList(Uuid $customerId) : CustomerAddressesListView
    {
        $query = $this->entityManager->createQuery("
            SELECT ca FROM Ntech\Customers\Models\SingleDoctrine\CustomerAddressDoctrineModel ca
            WHERE ca.customer = :customer
            ORDER BY ca.addedAt ASC
        ");
        $query->setParameter('customer', $this->entityManager->getReference(CustomerDoctrineModel::class, $customerId->toString()));
        $results = $query->execute();
        $viewAddresses = [];
        foreach ($results as $result) {
            $viewAddresses[] = CustomerAddress::fromDoctrineModel($result);
        }
        return new CustomerAddressesListView($viewAddresses);
    }

    public function getCustomerContactsList(Uuid $customerId)
    {
        if ($this->companyId != null) {
            $this->customerDoctrineRepository->forCompany($this->companyId);
        }
        $customer = $this->customerDoctrineRepository->get($customerId);
        $doctrineContacts = $customer->getContacts();

        $contacts = [];
        foreach ($doctrineContacts as $doctrineContact) {
            $contacts[] = CustomerContactListItem::fromDoctrineModel($doctrineContact);
        }
        return new CustomerContactsList($contacts);
    }

    public function getSingleCustomerContactModel(Uuid $contactId)
    {
        return $this->customerContactDoctrineRepository->get($contactId->toString());
    }
    
    public function getPaymentsSummaryForDashboard(Uuid $customerId)
    {
        if ($this->companyId != null) {
            $this->customerDoctrineRepository->forCompany($this->companyId);
        }
        $customer = $this->customerDoctrineRepository->get($customerId->toString());

        /** @var CompanyPaymentMethodCollection $paymentMethods */
        $companyPaymentMethods = $this->companyRepository
            ->findCompanyPaymentMethodsForCompany($customer->getCompany()->getId());

        $periodIncome = $this->paymentsRepository->getTotalForPeriod(
            Carbon::now()->subDays(30),
            Carbon::now(),
            ($this->companyId == null) ? $customer->getCompany()->getId() : $this->companyId,
            $customerId
        );

        $payments = $this->paymentsRepository->getPaymentsForCustomer(
            $customerId,
            new PaginateCriteria(1, 5, 'generatedAt')
        );
        $viewPayments = [];
        foreach ($payments as $recentPayment) {
            $companyMethodName = $companyPaymentMethods->get($recentPayment->getMethodKey())->getName();
            $paymentService = $this->paymentsServiceContainer->getUninitializedServiceObject(
                $recentPayment->getMethodKey()
            );
            $metadataPresenter = $paymentService->generatePaymentMethodMetadataPresenter($recentPayment->getMetadata());
            $viewPayments[] = new SinglePaymentSummary(
                $recentPayment->getId(),
                $recentPayment->getAmount(),
                $recentPayment->getMethodKey(),
                $companyMethodName,
                $recentPayment->getStatus(),
                $recentPayment->getGeneratedAt(),
                $metadataPresenter
            );
        }
        $customerCreditBalance = CustomerCreditBalanceView::fromDoctrine(
            app(EntityManager::class),
            $customerId
        );

        return new PaymentsSummaryForDashboardView(
            $viewPayments,
            new Amount($periodIncome, 'gbp'),
            $customerCreditBalance->getCredit()
        );
    }

    public function getDashboardSubscriptionsSummary(Uuid $customerId): SubscriptionsSummaryView
    {
        $subscriptions = $this->subscriptionsRepository->getSubscriptionsForCustomer($customerId, 1, 5);
        $recentSubscriptionsListItems = new SubscriptionsSummaryListItemCollection();
        foreach ($subscriptions as $subscription) {
            $recentSubscriptionsListItems->push(
                new SubscriptionSummaryListItem($subscription)
            );
        }
        $activeCount = $this->subscriptionsRepository->getActiveSubscriptionCountForCustomer($customerId);
        return new SubscriptionsSummaryView($recentSubscriptionsListItems, $activeCount);
    }
    
    public function getCustomerByGatewayReference(string $methodKey, string $customerReference)
    {
        /** @var GatewayCustomerModel $gatewayCustomerModel */
        $gatewayCustomerModel = $this->gatewayCustomerRepository
            ->getGatewayCustomerByReference($methodKey, $customerReference);
        return $this->getSingleCustomerModel($gatewayCustomerModel->getCustomerId());
    }

    public function getAllCustomersForCompany(Uuid $companyId)
    {
        return new CustomerCollection($this->customerDoctrineRepository->forCompany($companyId)->retrieve());
    }

}
