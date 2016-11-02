<?php
namespace Ntech\Customers\Commands\Import;

use Ntech\CommandBus\Command;
use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Commands\CreateCustomerCommand;
use Ntech\Customers\Events\CustomerCreated;
use Ntech\Customers\Customer;
use Ntech\Support\Address\Address;
use Ntech\Exceptions\NtechException;
use Ntech\Customers\Commands\UpdateCustomerCommand;

class CsvImportCustomersHandler extends CommandHandler
{

    public function handle(Command $command)
    {
        $csv = $command->getCsv();
        $fieldPairs = $this->purgeFieldsToIgnore($command->getFieldPairs());
        $rejected = [];
        
        $importFeedback = [
            'totals' => [
                'created' => 0,
                'updated' => 0,
                'rejected' => 0,
            ],
        ];
        $rowCount = 1;
        foreach ($csv as $customerRow) {
            $realValues = [];
            foreach ($fieldPairs as $slug => $valueKey) {
                if (is_array($valueKey)) {
                    foreach ($valueKey as $subSlug => $subValueKey) {
                        $realValues[$slug][$subSlug] = $customerRow[$subValueKey];
                    }
                } else {
                    $realValues[$slug] = $customerRow[$valueKey];
                }

            }
            try {
                $this->validateFields($realValues);
                $customer = $this->findCustomer($realValues);

                // If the record is unique we can create it
                if (is_null($customer)) {
                    $newCustomerCommand = new CreateCustomerCommand($command->getCompany(), $realValues['name']);
                    $newCustomerCommand->setContext($command->getContext());

                    if (isset($realValues['address'])) {
                        $newCustomerCommand->addAddress($this->processAddress($realValues['address']));
                        unset($realValues['address']);
                    }

                    $newCustomerCommand->setHydrationArray($realValues);
                    $newCustomerCommand->setEmail($realValues['email']);

                    app('command.bus')->handle($newCustomerCommand);
                    $importFeedback['totals']['created']++;
                } else {
                    // The record isnt unique, so we're updating
                    $updateCommand = new UpdateCustomerCommand($customer, $realValues);
                    $updateCommand->setContext($command->getContext());
                    // Check import policy
                    // If we ignore existing records
                    switch ($command->getUpdatePolicy()) {
                        case 'ignore':
                            throw new NtechException("Record already exists with email [{$realValues['email']}].", 1);
                            break;
                        
                        case 'merge':
                            $updateCommand->setUpdatePolicy($command->getUpdatePolicy());
                            break;

                        case 'overwrite':
                            $updateCommand->setUpdatePolicy($command->getUpdatePolicy());
                            break;
                    }

                    // Run the update customer command
                    app('command.bus')->handle($updateCommand);
                    $importFeedback['totals']['updated']++;

                }

            } catch (NtechException $e) {
                $importFeedback['rejected'][$rowCount] = [
                    'row' => $realValues,
                    'error' => $e->getMessage()
                ];
                $importFeedback['totals']['rejected']++;
            }
            $rowCount++;
        }
        return $importFeedback;
    }

    public function findCustomer(array $values)
    {
        if (!isset($values['email'])) {
            throw new NtechException("No Email provided with record", 1);
        }

        $customer = Customer::whereEmail($values['email'])->first();

        return $customer;
    }

    public function processAddress(array $addressArray)
    {
        $address = new Address([
            'street1' => array_get($addressArray, 'street1'),
            'street2' => array_get($addressArray, 'street2'),
            'postcode' => array_get($addressArray, 'postcode'),
            'city' => array_get($addressArray, 'city'),
            'country' => array_get($addressArray, 'country'),
            ]);

        return $address;
    }

    public function purgeFieldsToIgnore($fieldPairs)
    {
        foreach ($fieldPairs as $key => $value) {
            if ($value == -1) {
                unset($fieldPairs[$key]);
            }

            // If the field is a sub array, dive in to ignore anything within the array
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if ($subValue == -1) {
                        unset($fieldPairs[$key][$subKey]);
                    }
                }
                // If this array is now empty just get remove the array key from the field pairs
                if (empty($fieldPairs[$key])) {
                    unset($fieldPairs[$key]);
                }
            }
        }
        return $fieldPairs;
    }

    public function validateFields($values)
    {
        $validator = \Validator::make(
            $values,
            [
            'email' => 'required|email'
            ]
        );

        if ($validator->fails()) {
            throw new NtechException("Email is invalid", 1);
        }
    }
}
