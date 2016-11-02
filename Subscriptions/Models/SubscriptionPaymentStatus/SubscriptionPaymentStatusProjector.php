<?php
namespace Ntech\Subscriptions\Models\SubscriptionPaymentStatus;

use Doctrine\ORM\EntityManager;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel;
use Ntech\Invoices\Queries\GetInvoiceQuery;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineModel;
use Ntech\Payments\Queries\GetPaymentQuery;
use Ntech\Subscriptions\Events\SubscriptionDueInvoiceGenerated;
use Ntech\Subscriptions\Events\SubscriptionPaymentMade;
use Ntech\Subscriptions\Events\SubscriptionStarted;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;
use NtechUtility\Money\Amount;

class SubscriptionPaymentStatusProjector extends AbstractProjector
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;

    public function __construct(
        EntityManager $entityManager,
        QueryProcessor $queryProcessor
    ) {
        $this->entityManager = $entityManager;
        $this->queryProcessor = $queryProcessor;
    }

    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            SubscriptionStarted::class,
            SubscriptionDueInvoiceGenerated::class,
            SubscriptionPaymentMade::class
        ];
    }

    /**
     * Logic for deleting all projected data from this projection
     */
    public function delete()
    {
        $this->entityManager->getConnection()->exec("TRUNCATE subscriptions_payment_status;");
    }

    public function projectSubscriptionStarted(DomainEvent $domainEvent)
    {
        /** @var SubscriptionStarted $event */
        $event = $domainEvent->getPayload();
        
        $subPayStatus = new SubscriptionPaymentStatusModel(
            $this->entityManager->getReference(SubscriptionDoctrineModel::class, $event->getSubscriptionId()->toString()),
            new Amount(0, $event->getSubscriptionTerms()->getRate()->getCurrencyCode()),
            new Amount(0, $event->getSubscriptionTerms()->getRate()->getCurrencyCode())
        );
        $this->entityManager->persist($subPayStatus);
        $this->entityManager->flush();
    }
    
    public function projectSubscriptionDueInvoiceGenerated(DomainEvent $domainEvent)
    {
        /** @var SubscriptionDueInvoiceGenerated $event */
        $event = $domainEvent->getPayload();

        /** @var InvoiceDoctrineModel $invoice */
        $invoice = $this->queryProcessor->process(
            new GetInvoiceQuery(
                $event->getInvoiceId()
            )
        );

        $subPayStatus = $this->entityManager->getRepository(SubscriptionPaymentStatusModel::class)
            ->find($event->getSubscriptionId());

        $subPayStatus->addDebit($invoice->getTotal());

        $this->entityManager->persist($subPayStatus);
        $this->entityManager->flush();
    }

    public function projectSubscriptionPaymentMade(DomainEvent $domainEvent)
    {
        /** @var SubscriptionPaymentMade $event */
        $event = $domainEvent->getPayload();
        /** @var PaymentDoctrineModel $payment */
        $payment = $this->queryProcessor->process(
            new GetPaymentQuery($event->getPaymentId())
        );

        $subPayStatus = $this->entityManager->getRepository(SubscriptionPaymentStatusModel::class)
            ->find($event->getSubscriptionId());

        $subPayStatus->addCredit($payment->getAmount());
    }
}
