<?php
namespace Ntech\Subscriptions\Events\Listeners;

use Ntech\Exceptions\DomainException;
use Ntech\Payments\Events\PaymentAssignedToEntity;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineModel;
use Ntech\Payments\Payment;
use Ntech\Subscriptions\Events\SubscriptionPaymentMade;
use Ntech\Subscriptions\Payments\SubscriptionPaymentHandler;
use NtechUtility\EventSource\Domain\DomainEvent;
use NtechUtility\EventSource\EventBus\AbstractDomainEventListener;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class ProcessSubscriptionPaymentAndApplyToInvoice extends AbstractDomainEventListener
{

    /**
     * @var SubscriptionPaymentHandler
     */
    private $subscriptionPaymentHandler;

    public function __construct(
        SubscriptionPaymentHandler $subscriptionPaymentHandler
    ) {
        $this->subscriptionPaymentHandler = $subscriptionPaymentHandler;
    }

    /**
     * Array of event class names that this listener listens for
     * @return array
     */
    public function eventsListeningFor() : array
    {
        return [
            SubscriptionPaymentMade::class
        ];
    }

    public function listenSubscriptionPaymentMade(DomainEvent $domainEvent)
    {
        /** @var SubscriptionPaymentMade $event */
        $event = $domainEvent->getPayload();

        // Sort out applying that payment to an invoice
        $this->subscriptionPaymentHandler->handlePayment($event->getSubscriptionId(), $event->getPaymentId());
    }
}
