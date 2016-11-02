<?php
namespace Ntech\Customers\Commands\Import;

use Ntech\CommandBus\Command;
use Ntech\Companies\Company;
use Carbon\Carbon;
use Ntech\Support\Address\Address;

class CsvImportCustomersCommand extends Command
{

    protected $company;

    protected $csv;
    protected $fieldPairs;

    protected $updatePolicy = 1;

    const UPDATE_MERGE = 1;
    const UPDATE_OVERWRITE = 2;
    const UPDATE_IGNORE = 3;

    public function __construct(Company $company, array $csv, array $fieldPairs)
    {
        $this->company = $company;
        $this->csv = $csv;
        $this->fieldPairs = $fieldPairs;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getCsv()
    {
        return $this->csv;
    }

    public function getFieldPairs()
    {
        return $this->fieldPairs;
    }

    public function setUpdatePolicy($policy)
    {
        $this->updatePolicy = $policy;
    }

    public function getUpdatePolicy()
    {
        return $this->updatePolicy;
    }
}
