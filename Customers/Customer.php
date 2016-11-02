<?php
namespace Ntech\Customers;

use Carbon\Carbon;
use Ntech\Customers\Addresses\CustomerAddress;
use Ntech\Customers\Events\AddressAddedToCustomer;
use Ntech\Customers\Events\AddressRemovedFromCustomer;
use Ntech\Customers\Events\AliasSetForSavedPaymentMethod;
use Ntech\Customers\Events\ContactAddedToCustomer;
use Ntech\Customers\Events\ContactRemovedFromCustomer;
use Ntech\Customers\Events\ContactSetAsPrimary;
use Ntech\Customers\Events\CreditAddedToCustomer;
use Ntech\Customers\Events\CreditDebitedFromCustomer;
use Ntech\Customers\Events\CustomerBasicInfoModified;
use Ntech\Customers\Events\CustomerContactUpdated;
use Ntech\Customers\Events\CustomerEmailUpdated;
use Ntech\Customers\Events\CustomerLinkedToGatewayCustomer;
use Ntech\Customers\Events\CustomerRegistered;
use Ntech\Customers\Events\CustomerSavedPaymentMethodSetAsDefault;
use Ntech\Customers\Events\CustomerSetBillingAddress;
use Ntech\Customers\Events\CustomerSetPrimaryAddress;
use Ntech\Customers\Events\CustomerSetShippingAddress;
use Ntech\Customers\Events\CustomerUnlinkedFromGatewayCustomer;
use Ntech\Customers\Events\SavedPaymentMethodToCustomer;
use Ntech\Customers\Events\SavedCardSetAsSubscriptionCard;
use Ntech\Customers\Events\UserAddedToCustomer;
use Ntech\Customers\Events\UserRemovedFromCustomer;
use Ntech\Customers\Exceptions\CannotAddUserToCustomer;
use Ntech\Customers\Exceptions\CannotRemoveUserFromCustomer;
use Ntech\Customers\Payments\CustomerSavedPaymentMethods;
use Ntech\Invoices\CustomerInvoices;
use Ntech\Customers\Payments\CustomerPayments;
use Ntech\Payments\Methods\PaymentMethodMetadata;
use Ntech\Payments\Processing\Gateways\ReusableToken;
use Ntech\Support\Collections\Collection;
use NtechUtility\EventSource\EventSourcedAggregateRoot;
use NtechUtility\EventSource\EventSourcedAggregateRootTrait;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;

class Customer implements EventSourcedAggregateRoot
{
    use EventSourcedAggregateRootTrait;

    private $id;

    private $companyId;

    private $addresses;

    private $basicInfo;

    private $registeredOn;

    private $contacts;

    private $tags;

    private $invoices;

    private $payments;

    private $creditBalance;

    private $savedPaymentMethods;

    /**
     * Saved method ID for the stripe card to use for subscriptions
     * @var Uuid
     */
    private $subscriptionCard;

    private $email;

    /**
     * @var Collection
     */
    private $customerUsers;

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }

    /**
     * @param Uuid $customerId
     * @param Uuid $companyId
     * @param string $name
     * @param Carbon $customerSince
     * @return static
     */
    public static function register(Uuid $customerId, Uuid $companyId, string $name, Carbon $customerSince)
    {
        $customer = new static();

        $customer->apply(new CustomerRegistered($customerId, $companyId, $name, Carbon::now(), $customerSince));

        return $customer;
    }

    public function applyCustomerRegistered(CustomerRegistered $event)
    {
        $this->id = $event->getCustomerId();
        $this->companyId = $event->getCompanyId();
        $this->registeredOn = $event->getRegisteredOn();
        $this->basicInfo = new CustomerBasicInfo(
            $event->getCustomerName(),
            $event->getCustomerSince()
        );
        $this->contacts = new CustomerContacts();
        $this->addresses = new CustomerAddresses();
        $this->invoices = new CustomerInvoices();
        $this->payments = new CustomerPayments();
        $this->creditBalance = new CustomerCreditBalance();
        $this->savedPaymentMethods = new CustomerSavedPaymentMethods();
        $this->customerUsers = new Collection();
    }
    
    public function getChildEntities()
    {
        return [
            $this->contacts,
            $this->addresses,
            $this->invoices,
            $this->payments,
            $this->creditBalance,
            $this->savedPaymentMethods
        ];
    }

    public function addContact(
        CustomerContact $customerContact
    ) {
        $this->apply(new ContactAddedToCustomer(
            $this->id,
            $customerContact
        ));
    }

    public function updateContact(
        CustomerContact $customerContact
    ) {
        $this->apply(new CustomerContactUpdated(
            $this->id,
            $customerContact->getId(),
            $customerContact
        ));
    }

    public function removeContact(
        Uuid $contactId
    ) {
        $this->apply(
            new ContactRemovedFromCustomer(
                $this->id,
                $contactId
            )
        );
    }

    /**
     * Mark a contact as the primary contact
     * @param Uuid $contactId
     */
    public function setPrimaryContact(Uuid $contactId)
    {
        $this->apply(
            new ContactSetAsPrimary(
                $this->id,
                $contactId
            )
        );
    }

    /**
     * @param CustomerAddress $address
     * @param array $defaultsAs
     *        Array of integers defining what to set the added address as default
     *        for, ie. billing/shipping etc. using the CustomerAddress constants
     */
    public function addAddress(
        CustomerAddress $address
    ) {
        $this->apply(
            new AddressAddedToCustomer(
                $this->id,
                $address->getAddressId(),
                $address
            )
        );
    }

    public function removeAddress(Uuid $addressId)
    {
        $this->apply(
            new AddressRemovedFromCustomer(
                $this->id,
                $addressId
            )
        );
    }

    public function setPrimaryAddress(
        Uuid $addressId
    ) {
        $this->apply(
            new CustomerSetPrimaryAddress(
                $this->id,
                $addressId
            )
        );
    }

    public function setBillingAddress(
        Uuid $addressId
    ) {
        $this->apply(
            new CustomerSetBillingAddress(
                $this->id,
                $addressId
            )
        );
    }

    public function setShippingAddress(
        Uuid $addressId
    ) {
        $this->apply(
            new CustomerSetShippingAddress(
                $this->id,
                $addressId
            )
        );
    }

    public function addCredit(Amount $amount)
    {
        $this->apply(
            new CreditAddedToCustomer(
                $this->id,
                $amount
            )
        );
    }

    public function debitCredit(Amount $charge)
    {
        $this->apply(
            new CreditDebitedFromCustomer(
                $this->id,
                $charge
            )
        );
    }

    public function modifyBasicInfo(CustomerBasicInfo $newInfo)
    {
        $this->apply(
            new CustomerBasicInfoModified(
                $this->id,
                $newInfo
            )
        );
    }
    
    public function applyCustomerBasicInfoModified(CustomerBasicInfoModified $event)
    {
        $this->basicInfo = $event->getCustomerBasicInfo();
    }

    public function savePaymentMethod(
        Uuid $methodId,
        Uuid $companyId,
        Uuid $customerId,
        string $methodKey,
        ReusableToken $reusableToken,
        PaymentMethodMetadata $metadata,
        bool $subscribeable
    ) {
        $this->apply(
            new SavedPaymentMethodToCustomer(
                $methodId,
                $companyId,
                $customerId,
                $methodKey,
                $reusableToken,
                $metadata,
                $subscribeable
            )
        );

        // If this is the first/only method added set it as the default
        if ($this->savedPaymentMethods->getMethods()->count() == 1) {
            $this->apply(
                new CustomerSavedPaymentMethodSetAsDefault(
                    $this->id,
                    $methodId
                )
            );
        }
    }
    
    public function changeDefaultSavedPaymentMethod(Uuid $methodId)
    {
        $this->apply(
            new CustomerSavedPaymentMethodSetAsDefault(
                $this->id,
                $methodId
            )
        );
    }
    
    public function setAliasForPaymentMethod(Uuid $methodId, string $alias)
    {
        $this->apply(
            new AliasSetForSavedPaymentMethod(
                $this->id,
                $methodId,
                $alias
            )
        );
    }

    public function setSubscriptionCard(Uuid $methodId)
    {
        $this->apply(
            new SavedCardSetAsSubscriptionCard(
                $this->id,
                $methodId
            )
        );
    }
    
    public function applySavedCardSetAsSubscriptionCard(SavedCardSetAsSubscriptionCard $event)
    {
        $this->subscriptionCard = $event->getSavedMethodId();
    }

    public function updateEmail(string $newEmail)
    {
        $this->apply(
            new CustomerEmailUpdated(
                $this->id,
                $newEmail
            )
        );
    }

    public function applyCustomerEmailUpdated(CustomerEmailUpdated $event)
    {
        $this->email = $event->getNewEmail();
    }
    
    public function addUser(Uuid $userId)
    {
        if ($this->customerUsers->has($userId->toString())) {
            throw CannotAddUserToCustomer::becauseAlreadyAdded($this->id, $userId);
        }
        $this->apply(
            new UserAddedToCustomer(
                $this->id,
                $userId
            )
        );
    }
    
    public function applyUserAddedToCustomer(UserAddedToCustomer $event)
    {
        $this->customerUsers->put($event->getUserId()->toString(), []);
    }

    public function removeUser(Uuid $userId)
    {
        if (!$this->customerUsers->has($userId->toString())) {
            throw CannotRemoveUserFromCustomer::alreadyRemoved($this->id, $userId);
        }
        $this->apply(
            new UserRemovedFromCustomer(
                $this->id,
                $userId
            )
        );
    }

    public function applyUserRemovedFromCustomer(UserRemovedFromCustomer $event)
    {
        $this->customerUsers->forget($event->getUserId()->toString());
    }
}
