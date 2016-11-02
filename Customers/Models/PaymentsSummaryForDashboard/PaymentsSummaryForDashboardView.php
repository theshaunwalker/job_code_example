<?php
namespace Ntech\Customers\Models\PaymentsSummaryForDashboard;

use Ntech\Customers\Models\CustomerCreditBalance\CustomerCreditBalanceView;
use NtechUtility\Money\Amount;
use NtechUtility\Support\Collections\Collection;

class PaymentsSummaryForDashboardView
{
    /**
     * @var Collection
     */
    private $payments;
    /**
     * @var Amount
     */
    private $periodIncome;
    /**
     * @var Amount
     */
    private $creditBalance;

    public function __construct(
        array $payments,
        Amount $periodIncome,
        Amount $creditBalance
    ) {
        $this->payments = new Collection($payments);
        $this->periodIncome = $periodIncome;
        $this->creditBalance = $creditBalance;
    }
    
    public function getPayments()
    {
        return $this->payments;
    }

    public function getPeriodIncome()
    {
        return $this->periodIncome;
    }

    /**
     * @return Amount
     */
    public function getCreditBalance()
    {
        return $this->creditBalance;
    }

}
