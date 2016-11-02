<?php
namespace Ntech\Customers\Authorization;

use Ntech\Uuid\Uuid;

interface CustomerAuthorizer
{
    public function login(Uuid $userId, Uuid $companyId);

    public function logout();

    public function isLoggedIntoACustomer(): bool;

    public function getLoggedInCustomerId(): Uuid;

    public function getLoggedInCustomersCompanyId();

    public function canUserAccessCustomer(Uuid $userId, Uuid $companyId): bool;
}
