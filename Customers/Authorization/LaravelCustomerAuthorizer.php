<?php
namespace Ntech\Customers\Authorization;

use Illuminate\Session\SessionInterface;
use Ntech\Companies\Queries\GetCompanyQuery;
use Ntech\Customers\Exceptions\Authorization\UserDoesNotHaveAccessToCustomer;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Customers\Queries\GetCustomerQuery;
use NtechUtility\Cqrs\Query\QueryProcessor;
use Ntech\Uuid\Uuid;

class LaravelCustomerAuthorizer implements CustomerAuthorizer
{

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;

    public function __construct(
        SessionInterface $session,
        QueryProcessor $queryProcessor
    ) {
        $this->session = $session;
        $this->queryProcessor = $queryProcessor;
    }

    public function isLoggedIntoACustomer() : bool
    {
        return $this->session->has('customer');
    }

    public function getLoggedInCustomerId() : Uuid
    {
        return Uuid::fromString($this->session->get('customer.id'));
    }

    public function getLoggedInCustomersCompanyId()
    {
        return Uuid::fromString($this->session->get('customer.companyId'));
    }


    public function canUserAccessCustomer(Uuid $userId, Uuid $customerId) : bool
    {
        $customer = $this->queryProcessor->process(
            new GetCustomerQuery($customerId)
        );

        if ($customer->hasUser($userId)) {
            // Log them in
            return true;
        }

        // User isnt explicitly set as a user of the customer's account
        // So double check if user is in the company which this customer is for
        $company = $this->queryProcessor->process(
            new GetCompanyQuery($customer->getCompanyId())
        );

        if ($company->hasUser($userId)) {
            // Log them in
            return true;
        }

        return false;
    }

    public function login(Uuid $userId, Uuid $customerId)
    {
        if ($this->canUserAccessCustomer($userId, $customerId)) {
            $this->session->set('customer.id', $customerId->toString());
            /** @var CustomerDoctrineModel $customer */
            $customer = $this->queryProcessor->process(
                new GetCustomerQuery($customerId)
            );
            $this->session->set('customer.companyId', $customer->getCompanyId());
            return;
        }
        throw UserDoesNotHaveAccessToCustomer::because("User does not have access to this customer");
    }

    public function logout()
    {
        $this->session->remove('customer');
    }
}
