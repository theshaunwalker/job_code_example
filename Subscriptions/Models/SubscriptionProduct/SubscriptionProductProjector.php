<?php
namespace Ntech\Subscriptions\Models\SubscriptionProduct;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductCreated;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductInfoModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTermsModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierCreated;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierDeleted;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierInfoModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionCreated;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionDeleted;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionInfoModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierPaymentOptionTermsModified;
use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductTierRetired;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;

class SubscriptionProductProjector extends AbstractProjector
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $subscriptionProductRepo;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $tiersRepo;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $paymentOptionRepo;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionProductRepo = $entityManager->getRepository(SubscriptionProductModel::class);
        $this->tiersRepo = $entityManager->getRepository(SubscriptionProductTierModel::class);
        $this->paymentOptionRepo = $entityManager->getRepository(SubscriptionProductTierPaymentOptionModel::class);
    }

    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            SubscriptionProductCreated::class,
            SubscriptionProductInfoModified::class,
            SubscriptionProductTierCreated::class,
            SubscriptionProductTierInfoModified::class,
            SubscriptionProductTierDeleted::class,
            SubscriptionProductTierRetired::class,
            SubscriptionProductTierPaymentOptionCreated::class,
            SubscriptionProductTierPaymentOptionDeleted::class,
            SubscriptionProductTierPaymentOptionTermsModified::class
        ];
    }

    /**
     * Logic for deleting all projected data from this projection
     */
    public function delete()
    {
        $this->entityManager->getConnection()->exec("TRUNCATE subscriptions_products;");
        $this->entityManager->getConnection()->exec("TRUNCATE subscriptions_products_tiers;");
        $this->entityManager->getConnection()->exec("TRUNCATE subscriptions_products_tiers_payment_options;");
    }
    
    public function projectSubscriptionProductCreated(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductCreated $event */
        $event = $domainEvent->getPayload();

        $product = new SubscriptionProductModel(
            $event->getId(),
            $this->entityManager->getReference(CompanyDoctrineModel::class, $event->getCompanyId()),
            $event->getInfo(),
            Carbon::instance($domainEvent->getRecordedOn())
        );
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
    
    public function projectSubscriptionProductInfoModified(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductInfoModified $event */
        $event = $domainEvent->getPayload();
        
        $product = $this->subscriptionProductRepo->find($event->getSubProductId()->toString());
        $product->modifyInfo($event->getInfo());
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTermsModified(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTermsModified $event */
        $event = $domainEvent->getPayload();

        $product = $this->subscriptionProductRepo->find($event->getSubscriptionProductId()->toString());
        $product->modifyTerms($event->getSubTerms());
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTierCreated(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTierCreated $event */
        $event = $domainEvent->getPayload();

        $tierModel = new SubscriptionProductTierModel(
            $event->getTier(),
            $this->entityManager->getReference(SubscriptionProductModel::class, $event->getSubscriptionProductId()->toString())
        );
        $this->entityManager->persist($tierModel);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTierDeleted(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTierDeleted $event */
        $event = $domainEvent->getPayload();

        $tier = $this->tiersRepo->find($event->getTierId()->toString());
        $this->entityManager->remove($tier);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTierRetired(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTierRetired $event */
        $event = $domainEvent->getPayload();

        $tier = $this->tiersRepo->find($event->getTierId()->toString());
        $tier->setRetired(true);
        $this->entityManager->persist($tier);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTierPaymentOptionCreated(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTierPaymentOptionCreated $event */
        $event = $domainEvent->getPayload();

        $paymentOptionModel = new SubscriptionProductTierPaymentOptionModel(
            $event->getPaymentOption(),
            $this->entityManager->getReference(SubscriptionProductTierModel::class, $event->getTierId()->toString())
        );
        $this->entityManager->persist($paymentOptionModel);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTierPaymentOptionDeleted(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTierPaymentOptionDeleted $event */
        $event = $domainEvent->getPayload();

        $paymentOption = $this->paymentOptionRepo->find($event->getPaymentOptionId()->toString());
        $this->entityManager->remove($paymentOption);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTierPaymentOptionTermsModified(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTierPaymentOptionTermsModified $event */
        $event = $domainEvent->getPayload();

        $paymentOption = $this->paymentOptionRepo->find($event->getPaymentOptionId()->toString());
        $paymentOption->setTerms($event->getNewTerms());
        $this->entityManager->persist($paymentOption);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTierPaymentOptionInfoModified(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTierPaymentOptionInfoModified $event */
        $event = $domainEvent->getPayload();

        $paymentOption = $this->paymentOptionRepo->find($event->getPaymentOptionId()->toString());
        $paymentOption->setInfo($event->getNewInfo());
        $this->entityManager->persist($paymentOption);
        $this->entityManager->flush();
    }

    public function projectSubscriptionProductTierInfoModified(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductTierInfoModified $event */
        $event = $domainEvent->getPayload();

        $tier = $this->tiersRepo->find($event->getTierId()->toString());
        $tier->setInfo($event->getTierInfo());
        $this->entityManager->persist($tier);
        $this->entityManager->flush();
    }
}
