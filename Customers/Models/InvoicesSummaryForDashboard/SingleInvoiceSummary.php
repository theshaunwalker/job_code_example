<?php
namespace Ntech\Customers\Models\InvoicesSummaryForDashboard;

use Carbon\Carbon;
use Ntech\Invoices\Invoice;
use Ntech\Invoices\Models\Traits\InvoiceHtmlBadgeData;
use Ntech\Uuid\Uuid;
use NtechUtility\Money\Amount;

class SingleInvoiceSummary
{
    use InvoiceHtmlBadgeData;
    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var string
     */
    private $title;
    /**
     * @var Amount
     */
    private $total;
    /**
     * @var Carbon
     */
    private $dueDate;
    /**
     * @var int
     */
    private $status;

    public function __construct(
        Uuid $id,
        string $title,
        Amount $total,
        Carbon $dueDate,
        int $status
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->total = $total;
        $this->dueDate = $dueDate;
        $this->status = $status;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return Amount
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return Carbon
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }
    
    public function isDue()
    {
        return $this->dueDate <= Carbon::now();
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getBadgeDataStatus()
    {
        return $this->getStatus();
    }

    public function getInvoiceDueDate()
    {
        return $this->getDueDate();
    }
}
