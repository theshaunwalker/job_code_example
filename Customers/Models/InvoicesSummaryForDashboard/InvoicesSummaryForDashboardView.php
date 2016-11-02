<?php
namespace Ntech\Customers\Models\InvoicesSummaryForDashboard;

use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use NtechUtility\Money\Amount;
use NtechUtility\Support\Collections\Collection;

class InvoicesSummaryForDashboardView
{
    /**
     * @var Collection
     */
    private $invoices;
    /**
     * @var string
     */
    private $totalOutstanding;

    public function __construct(
        Collection $invoices,
        Amount $totalOutstanding
    ) {
        $this->invoices = $invoices;
        $this->totalOutstanding = $totalOutstanding->readable();
    }

    public static function fromSingleCustomerModel(CustomerDoctrineModel $customer)
    {
        $doctrineInvoices = $customer->getInvoices()->slice(0, 5, true);
        $invoices = new Collection();
        foreach ($doctrineInvoices as $doctrineInvoice) {
            $invoices->push(
                new SingleInvoiceSummary(
                    $doctrineInvoice->getId(),
                    $doctrineInvoice->getTitle(),
                    $doctrineInvoice->getTotal(),
                    $doctrineInvoice->getDueDate(),
                    $doctrineInvoice->getStatus()
                )
            );
        }
        $view = new static(
            $invoices,
            $doctrineInvoices->getTotalOutstanding()
        );
        return $view;
    }

    /**
     * @return Collection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @return string
     */
    public function getTotalOutstanding()
    {
        return $this->totalOutstanding;
    }
}
