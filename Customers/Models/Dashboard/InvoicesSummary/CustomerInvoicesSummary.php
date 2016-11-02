<?php
namespace Ntech\Customers\Models\Dashboard\InvoicesSummary;

use Carbon\Carbon;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceCollection;
use NtechUtility\Money\Amount;

class CustomerInvoicesSummary
{
    /**
     * @var InvoiceCollection
     */
    private $recentInvoices;
    /**
     * @var InvoiceCollection
     */
    private $windowInvoices;
    /**
     * @var Carbon
     */
    private $windowDate;

    public function __construct(
        InvoiceCollection $recentInvoices,
        InvoiceCollection $windowInvoices,
        Carbon $windowDate
    ) {
        $this->recentInvoices = $recentInvoices;
        $this->windowInvoices = $windowInvoices;
        $this->windowDate = $windowDate;
    }

    /**
     * @return InvoiceCollection
     */
    public function getRecentInvoices()
    {
        return $this->recentInvoices;
    }

    public function getTotalOutstanding(): Amount
    {
        return $this->windowInvoices->getTotalOutstanding();
    }

}
