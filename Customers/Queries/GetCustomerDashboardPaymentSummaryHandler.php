<?php
namespace Ntech\Customers\Queries;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Ntech\Companies\Queries\Payments\GetAllCompanyPaymentMethodsQuery;
use Ntech\Customers\Models\CustomerCreditBalance\CustomerCreditBalanceView;
use Ntech\Customers\Models\PaymentsSummaryForDashboard\PaymentsSummaryForDashboardView;
use Ntech\Customers\Models\PaymentsSummaryForDashboard\SinglePaymentSummary;
use Ntech\Payments\Models\Payment\PaymentCollection;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineModel;
use Ntech\Payments\Payment;
use Ntech\Payments\PaymentsServiceContainer;
use Ntech\Payments\Queries\GetPaymentsTotalForPeriodQuery;
use NtechUtility\Cqrs\Query\QueryHandler;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\Money\Amount;

class GetCustomerDashboardPaymentSummaryHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;
    /**
     * @var PaymentsServiceContainer
     */
    private $paymentsServiceContainer;

    public function __construct(
        EntityManager $entityManager,
        QueryProcessor $queryProcessor,
        PaymentsServiceContainer $paymentsServiceContainer
    ) {
        $this->entityManager = $entityManager;
        $this->queryProcessor = $queryProcessor;
        $this->paymentsServiceContainer = $paymentsServiceContainer;
    }
    
    public function handle(GetCustomerDashboardPaymentSummaryQuery $query)
    {
        $customer = $this->queryProcessor->process(
            new GetCustomerQuery($query->getCustomerId())
        );

        /** @var CompanyPaymentMethodCollection $paymentMethods */
        $companyPaymentMethods = $this->queryProcessor->process(
            new GetAllCompanyPaymentMethodsQuery($customer->getCompanyId())
        );

        $periodIncome = $this->queryProcessor->process(
            new GetPaymentsTotalForPeriodQuery(
                Carbon::now()->subDays(30),
                Carbon::now(),
                $customer->getCompanyId(),
                $query->getCustomerId()
            )
        );

        $payments = $this->entityManager->getRepository(PaymentDoctrineModel::class)->createQueryBuilder('p')
            ->where('p.customer = :customer')
            ->setParameters([
                'customer' => $customer->getId()->toString()
            ])
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
        $payments = new PaymentCollection($payments);

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
            $query->getCustomerId()
        );

        return new PaymentsSummaryForDashboardView(
            $viewPayments,
            new Amount($periodIncome, 'gbp'),
            $customerCreditBalance->getCredit()
        );
    }
}
