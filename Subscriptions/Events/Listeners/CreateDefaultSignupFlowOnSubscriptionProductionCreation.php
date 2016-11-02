<?php
namespace Ntech\Subscriptions\Events\Listeners;

use Ntech\Subscriptions\Events\SubscriptionProduct\SubscriptionProductCreated;
use Ntech\Subscriptions\Products\SignupFlow\SignupSettings;
use Ntech\Subscriptions\Products\SignupFlow\SubscriptionProductSignupFlow;
use Ntech\Uuid\UuidGenerator;
use NtechUtility\EventSource\Domain\DomainEvent;
use NtechUtility\EventSource\EventBus\AbstractDomainEventListener;
use NtechUtility\EventSource\Repository\EventSourcingRepository;
use NtechUtility\EventSource\Repository\EventSourcingRepositoryFactoryInterface;

class CreateDefaultSignupFlowOnSubscriptionProductionCreation extends AbstractDomainEventListener
{
    /**
     * @var EventSourcingRepository
     */
    private $signupFlowSourceRepo;
    /**
     * @var UuidGenerator
     */
    private $uuidGenerator;

    public function __construct(
        EventSourcingRepositoryFactoryInterface $sourceFactory,
        UuidGenerator $uuidGenerator
    ) {
        $this->signupFlowSourceRepo = $sourceFactory->forAggregate(SubscriptionProductSignupFlow::class);
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * Array of event class names that this listener listens for
     * @return array
     */
    public function eventsListeningFor() : array
    {
        return [
            SubscriptionProductCreated::class
        ];
    }

    public function listenSubscriptionProductCreated(DomainEvent $domainEvent)
    {
        /** @var SubscriptionProductCreated $event */
        $event = $domainEvent->getPayload();

        $signupFlow = SubscriptionProductSignupFlow::newFlow(
            $this->uuidGenerator->uuid4(),
            $event->getId(),
            'default',
            new SignupSettings()
        );
        $this->signupFlowSourceRepo->save($signupFlow);
    }
}
