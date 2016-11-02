<?php
namespace Ntech\Subscriptions\Models\Subscription;

use Doctrine\ORM\EntityManager;
use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineModel;
use Ntech\Payments\Models\PaymentSubscription\PaymentSubscriptionModel;
use Ntech\Subscriptions\Events\PaymentMethodAttachedToSubscription;
use Ntech\Subscriptions\Events\SubscriptionAttachedToSubscriptionProduct;
use Ntech\Subscriptions\Events\SubscriptionCancelled;
use Ntech\Subscriptions\Events\SubscriptionExpirationRemoved;
use Ntech\Subscriptions\Events\SubscriptionPaymentMade;
use Ntech\Subscriptions\Events\SubscriptionPeriodEnded;
use Ntech\Subscriptions\Events\SubscriptionPeriodStarted;
use Ntech\Subscriptions\Events\SubscriptionReactivated;
use Ntech\Subscriptions\Events\SubscriptionSetToExpire;
use Ntech\Subscriptions\Events\SubscriptionStarted;
use Ntech\Subscriptions\Events\SubscriptionSuspended;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductTierModel;
use Ntech\Subscriptions\Subscription;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;

class SubscriptionProjector extends AbstractProjector
{

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $subscriptionEntityRepository;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $subscriptionPeriodEntityRepository;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionEntityRepository = $this->entityManager->getRepository(SubscriptionDoctrineModel::class);
        $this->subscriptionPeriodEntityRepository = $this->entityManager->getRepository(SubscriptionPeriodDoctrineModel::class);
    }
    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            SubscriptionStarted::class,
            SubscriptionSetToExpire::class,
            SubscriptionPeriodStarted::class,
            SubscriptionPeriodEnded::class,
            SubscriptionSuspended::class,
            SubscriptionCancelled::class,
            SubscriptionReactivated::class,
            PaymentMethodAttachedToSubscription::class,
            SubscriptionAttachedToSubscriptionProduct::class,
            SubscriptionPaymentMade::class
        ];
    }

    /**
     * Logic for deleting all projected data from this projection
     */
    public function delete()
    {
        $this->entityManager->getConnection()->exec('TRUNCATE subscriptions;');
        $this->entityManager->getConnection()->exec('TRUNCATE subscriptions_periods;');
    }
    
    public function projectSubscriptionStarted(DomainEvent $domainEvent)
    {
        /** @var SubscriptionStarted $event */
        $event = $domainEvent->getPayload();
        $companyRef = $this->entityManager->getReference(
            CompanyDoctrineModel::class,
            $event->getCompanyId()->toString()
        );
        $customerRef = $this->entityManager->getReference(
            CustomerDoctrineModel::class,
            $event->getCustomerId()->toString()
        );

        $subscription = new SubscriptionDoctrineModel(
            $event->getSubscriptionId(),
            $companyRef,
            $customerRef,
            $event->getName(),
            $event->getStartDate(),
            $event->getSubscriptionTerms(),
            Subscription::STATUS_ACTIVE
        );

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

    }

    public function projectSubscriptionSetToExpire(DomainEvent $domainEvent)
    {
        /** @var SubscriptionSetToExpire $event */
        $event = $domainEvent->getPayload();

        $subscription = $this->subscriptionEntityRepository->find($event->getSubscriptionId()->toString());

        $subscription->setExpiration($event->getExpiration());

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }
    
    public function projectSubscriptionExpirationRemoved(DomainEvent $domainEvent)
    {
        /** @var SubscriptionExpirationRemoved $event */
        $event = $domainEvent->getPayload();

        $subscription = $this->subscriptionEntityRepository->find($event->getSubscriptionId()->toString());

        $subscription->removeExpiration();

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function projectSubscriptionSuspended(DomainEvent $domainEvent)
    {
        /** @var SubscriptionSuspended $event */
        $event = $domainEvent->getPayload();

        $subscription = $this->subscriptionEntityRepository->find($event->getSubscriptionId()->toString());

        $subscription->setStatus(Subscription::STATUS_SUSPENDED);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }


    public function projectSubscriptionCancelled(DomainEvent $domainEvent)
    {
        /** @var SubscriptionCancelled $event */
        $event = $domainEvent->getPayload();

        $subscription = $this->subscriptionEntityRepository->find($event->getSubscriptionId()->toString());

        $subscription->setStatus(Subscription::STATUS_CANCELLED);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function projectSubscriptionReactivated(DomainEvent $domainEvent)
    {
        /** @var SubReac $event */
        $event = $domainEvent->getPayload();

        $subscription = $this->subscriptionEntityRepository->find($event->getSubscriptionId()->toString());

        $subscription->setStatus(Subscription::STATUS_ACTIVE);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function projectSubscriptionPeriodStarted(DomainEvent $domainEvent)
    {
        /** @var SubscriptionPeriodStarted $event */
        $event = $domainEvent->getPayload();

        $period = $event->getPeriod();

        $periodModel = new SubscriptionPeriodDoctrineModel(
            $this->entityManager->getReference(SubscriptionDoctrineModel::class, $event->getSubscriptionId()->toString()),
            $period->getOrderCount(),
            $period->getStartDate(),
            $period->getEndDate()
        );

        $this->entityManager->persist($periodModel);

        $subscription = $this->subscriptionEntityRepository->find($event->getSubscriptionId()->toString());
        $subscription->setRenewalDate($period->getEndDate());
        $subscription->setCurrentPeriod($period->getOrderCount());

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function projectSubscriptionPeriodEnded(DomainEvent $domainEvent)
    {
        /** @var SubscriptionPeriodEnded $event */
        $event = $domainEvent->getPayload();

        // We do nothing with the database
    }

    public function projectPaymentMethodAttachedToSubscription(DomainEvent $domainEvent)
    {
        /** @var PaymentMethodAttachedToSubscription $event */
        $event = $domainEvent->getPayload();

        /** @var SubscriptionDoctrineModel $subscription */
        $subscription = $this->subscriptionEntityRepository->find($event->getSubscriptionId()->toString());
        
        $subscription->setPaymentMethod(
            $this->entityManager->getReference(
                PaymentSubscriptionModel::class,
                $event->getPaymentSubscriptionId()->toString()
            )
        );

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function projectSubscriptionAttachedToSubscriptionProduct(DomainEvent $domainEvent)
    {
        /** @var SubscriptionAttachedToSubscriptionProduct $event */
        $event = $domainEvent->getPayload();

        $subscription = $this->subscriptionEntityRepository->find($event->getSubscriptionId()->toString());

        $subscription->setSubscriptionProduct(
            $this->entityManager->getReference(
                SubscriptionProductModel::class,
                $event->getSubscriptionProductId()->toString()
            ),
            $this->entityManager->getReference(
                SubscriptionProductTierModel::class,
                $event->getTierId()->toString()
            ),
            $this->entityManager->getReference(
                SubscriptionProductModel::class,
                $event->getTierPaymentOptionId()->toString()
            )
        );

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function projectSubscriptionPaymentMade(DomainEvent $domainEvent)
    {
        /** @var SubscriptionPaymentMade $event */
        $event = $domainEvent->getPayload();

        $subscriptionPayment = new SubscriptionPaymentModel(
            $event->getSubscriptionId(),
            $event->getPaymentId()
        );
        $this->entityManager->persist($subscriptionPayment);
        $this->entityManager->flush();
    }
}
