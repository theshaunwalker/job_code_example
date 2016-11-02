<?php
namespace Ntech\Customers\Payments;

use Ntech\Customers\Events\AliasSetForSavedPaymentMethod;
use Ntech\Customers\Events\CustomerSavedPaymentMethodSetAsDefault;
use Ntech\Customers\Events\PaymentMethodSetAsDefaultForCustomer;
use Ntech\Customers\Events\SavedPaymentMethodToCustomer;
use Ntech\Customers\Events\SavedCardSetAsSubscriptionCard;
use Ntech\Exceptions\DomainException;
use Ntech\Payments\Methods\PaymentMethodMetadata;
use Ntech\Payments\Methods\SavedPaymentMethodMetadata;
use NtechUtility\EventSource\EventSourcedEntity;
use NtechUtility\EventSource\EventSourcedEntityTrait;
use NtechUtility\Support\Collections\Collection;

class CustomerSavedPaymentMethods implements EventSourcedEntity
{
    use EventSourcedEntityTrait;

    private $methods;

    private $defaultChargeMethod;

    private $cardForSubscriptions;

    public function __construct()
    {
        $this->methods = new Collection();
    }

    /**
     * @return Collection
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return mixed
     */
    public function getDefaultMethod()
    {
        return $this->defaultMethod;
    }


    public function applySavedPaymentMethodToCustomer(SavedPaymentMethodToCustomer $event)
    {
        $method = new CustomerSavedPaymentMethod(
            $event->getSavedMethodId(),
            $event->getCompanyId(),
            $event->getCustomerId(),
            $event->getPaymentMethodKey(),
            $event->getToken(),
            $event->getMetadata()
        );
        $this->methods->put($method->getSavedMethodId(), $method);
    }
    
    public function applyCustomerSavedPaymentMethodSetAsDefault(CustomerSavedPaymentMethodSetAsDefault $event)
    {
        $this->defaultChargeMethod = $event->getSavedMethodId();
    }
    
    public function applyAliasSetForSavedPaymentMethod(AliasSetForSavedPaymentMethod $event)
    {
        $method = $this->methods->get($event->getSavedMethodId()->toString());

        $method->setAlias($event->getAlias());
        
        $this->methods->put($method->getSavedMethodId()->toString(), $method);
    }
    
    public function applySetSavedCardAsSubscriptionCard(SavedCardSetAsSubscriptionCard $event)
    {
        $methodId = $event->getSavedMethodId();

        /** @var CustomerSavedPaymentMethod $method */
        $method = $this->methods->get($methodId->toString());

        if ($method->getMethodKey() != 'stripe') {
            throw DomainException::because("Cannot set subscription card because selected method is not a card.");
        }

        $this->cardForSubscriptions = $methodId;
    }
}
