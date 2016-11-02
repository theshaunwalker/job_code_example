<?php
namespace Ntech\Subscriptions;

use Ntech\Invoices\InvoiceRepository;
use Ntech\Payments\Models\PaymentSubscription\PaymentSubscriptionModel;
use Ntech\Payments\Models\PaymentSubscription\PaymentSubscriptionRepository;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineRepository;
use Ntech\Subscriptions\Models\Subscription\SubscriptionPeriodDoctrineRepository;
use Ntech\Subscriptions\Models\SubscriptionDues\SubscriptionDuesRepository;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\PaginateCriteria;
use NtechUtility\Support\Collections\PaginatedCollection;

class SubscriptionsRepository
{
    /**
     * @var SubscriptionDoctrineRepository
     */
    private $subscriptionReadRepository;
    /**
     * @var SubscriptionDuesRepository
     */
    private $subscriptionDuesRepository;
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;
    /**
     * @var PaymentSubscriptionRepository
     */
    private $paymentSubscriptionRepository;
    /**
     * @var SubscriptionPeriodDoctrineRepository
     */
    private $subscriptionPeriodDoctrineRepository;

    public function __construct(
        SubscriptionDoctrineRepository $subscriptionReadRepository,
        SubscriptionDuesRepository $subscriptionDuesRepository,
        InvoiceRepository $invoiceRepository,
        PaymentSubscriptionRepository $paymentSubscriptionRepository,
        SubscriptionPeriodDoctrineRepository $subscriptionPeriodDoctrineRepository
    ) {
        $this->subscriptionReadRepository = $subscriptionReadRepository;
        $this->subscriptionDuesRepository = $subscriptionDuesRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentSubscriptionRepository = $paymentSubscriptionRepository;
        $this->subscriptionPeriodDoctrineRepository = $subscriptionPeriodDoctrineRepository;
    }
    
    public function getSubscription(Uuid $subscriptionId)
    {
        return $this->subscriptionReadRepository->get($subscriptionId->toString());
    }

    public function getAllSubscriptionsForCustomer(Uuid $customerId)
    {
        return $this->subscriptionReadRepository->getAllForCustomer($customerId);
    }

    public function getSubscriptionsForCustomer(Uuid $customerId, $page, $limit)
    {
        $paginateCriteria = new PaginateCriteria($page, $limit);
        $results = $this->subscriptionReadRepository->getForCustomer($customerId, $paginateCriteria);
        return $results;
    }

    public function getActiveSubscriptionCountForCustomer(Uuid $customerId)
    {
        return $this->subscriptionReadRepository->getActiveSubscriptionCountForCustomer($customerId);
    }

    public function getSuspendedSubscriptionCountForCustomer(Uuid $customerId)
    {
        return $this->subscriptionReadRepository->getSuspendedSubscriptionCountForCustomer($customerId);
    }

    public function getCancelledSubscriptionCountForCustomer(Uuid $customerId)
    {
        return $this->subscriptionReadRepository->getCancelledSubscriptionCountForCustomer($customerId);
    }

    public function getSubscriptionDueForPeriod(Uuid $subscriptionId, int $periodId)
    {
        return $this->subscriptionDuesRepository->getSubscriptionDueForPeriod($subscriptionId, $periodId);
    }
    
    public function getSubscriptionInvoices(Uuid $subscriptionId)
    {
        return $this->invoiceRepository->getInvoicesForEntity($subscriptionId, Subscription::class);
    }
    
    public function getSubscriptionForGatewayReference(string $methodKey, string $gatewayId)
    {
        /** @var PaymentSubscriptionModel $paymentSubscriptionModel */
        $paymentSubscriptionModel = $this->paymentSubscriptionRepository
            ->getPaymentSubscriptionForGatewayReference($methodKey, $gatewayId);
        $subscriptionModel = $this->getSubscription($paymentSubscriptionModel->getSubscriptionId());
        return $subscriptionModel;
    }
}
