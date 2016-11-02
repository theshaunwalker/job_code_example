<?php
namespace Ntech\Customers;

use Ntech\Customers\Events\CreditAddedToCustomer;
use Ntech\Customers\Events\CreditDebitedFromCustomer;
use NtechUtility\EventSource\EventSourcedEntity;
use NtechUtility\EventSource\EventSourcedEntityTrait;
use NtechUtility\Money\Amount;

class CustomerCreditBalance implements EventSourcedEntity
{
    use EventSourcedEntityTrait;

    private $creditAmount = 0;

    /**
     * Credit the balance with an amount
     * @param Amount $amount
     */
    public function credit(Amount $amount)
    {
        $this->creditAmount += $amount->getAmount();
    }

    /**
     * Debit the balance with an amount
     * @param Amount $amount
     */
    public function debit(Amount $amount)
    {
        $this->creditAmount -= $amount->getAmount();
    }

    public function applyCreditAddedToCustomer(CreditAddedToCustomer $event)
    {
        $this->credit($event->getAmount());
    }

    public function applyCreditDebitedFromCustomer(CreditDebitedFromCustomer $event)
    {
        $this->debit($event->getAmount());
    }
}
