<?php
namespace Ntech\Subscriptions\Models;

use Doctrine\ORM\EntityManager;
use Ntech\Subscriptions\Models\Subscription\SubscriptionProjector;
use Ntech\Subscriptions\Models\SubscriptionDues\SubscriptionDueProjector;
use Ntech\Subscriptions\Models\SubscriptionPaymentStatus\SubscriptionPaymentStatusProjector;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductProjector;
use Ntech\Subscriptions\Models\SubscriptionProductSignupFlow\SubscriptionProductSignupFlowProjector;
use Ntech\Subscriptions\SubscriptionsRepository;
use NtechUtility\Cqrs\Query\QueryProcessor;
use NtechUtility\Cqrs\ReadModel\AbstractProjectorRegister;

class SubscriptionProjectorRegister extends AbstractProjectorRegister
{

    public function bootstrapProjectors($container)
    {
        $entityManager = $container->make(EntityManager::class);
        $queryProcessor = $container->make(QueryProcessor::class);
        $this->addProjector(
            new SubscriptionProjector(
                $entityManager
            )
        );
        $this->addProjector(
            new SubscriptionDueProjector(
                $entityManager,
                $container->make(SubscriptionsRepository::class)
            )
        );
        $this->addProjector(
            new SubscriptionProductProjector(
                $entityManager
            )
        );
        $this->addProjector(
            new SubscriptionProductSignupFlowProjector(
                $entityManager,
                app('event.store.uuid.generator')
            )
        );
        $this->addProjector(
            new SubscriptionPaymentStatusProjector(
                $entityManager,
                $queryProcessor
            )
        );
    }
}
