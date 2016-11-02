<?php
namespace Ntech\Customers\Models\PaymentsSummaryForDashboard;

use Carbon\Carbon;
use Ntech\Companies\Models\CompanyPaymentMethods\CompanyPaymentMethodCollection;
use Ntech\Payments\Methods\PaymentMethodMetadataPresenter;
use Ntech\Payments\Models\Payment\PaymentStatusBadgePresenter;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineModel;
use Ntech\Payments\PaymentsServiceContainer;
use Ntech\Support\ValueObjects\ValueObject;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;

class SinglePaymentSummary extends ValueObject
{

    protected $id;

    protected $amount;

    protected $methodKey;

    /**
     * @var string
     */
    protected $methodName;

    protected $status;
    protected $generatedAt;
    /**
     * @var PaymentMethodMetadataPresenter
     */
    protected $methodMetadataPresenter;

    public function __construct(
        Uuid $id,
        Amount $amount,
        string $methodKey,
        string $methodName,
        int $status,
        Carbon $generatedAt,
        PaymentMethodMetadataPresenter $methodMetadataPresenter
    ) {
        $this->id = $id;
        $this->amount = $amount;
        $this->methodKey = $methodKey;
        $this->methodName = $methodName;
        $this->status = $status;
        $this->generatedAt = $generatedAt;
        $this->methodMetadataPresenter = $methodMetadataPresenter;
    }

    public static function fromDoctrineModel(
        PaymentDoctrineModel $paymentModel,
        CompanyPaymentMethodCollection $companyPaymentMethods,
        PaymentsServiceContainer $paymentsServiceContainer
    ) {
        $companyMethodName = $companyPaymentMethods->get($paymentModel->getMethodKey())->getName();
        $paymentService = $paymentsServiceContainer->getUninitializedServiceObject(
            $paymentModel->getMethodKey()
        );
        $metadataPresenter = $paymentService->generatePaymentMethodMetadataPresenter($paymentModel->getMetadata());
        $viewPayments[] = new SinglePaymentSummary(
            $paymentModel->getId(),
            $paymentModel->getAmount(),
            $paymentModel->getMethodKey(),
            $companyMethodName,
            $paymentModel->getStatus(),
            $paymentModel->getGeneratedAt(),
            $metadataPresenter
        );
    }

    public function getStatusBadgePresenter()
    {
        return new PaymentStatusBadgePresenter($this->getStatus());
    }

}
