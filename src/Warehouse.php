<?php

namespace Warehouse;
use Warehouse\Validator;

/**
 * Description of Warehouse
 *
 * @author jurgis
 */
class Warehouse {
    
    /**
     * @var array
     */
    private $pickRun = [];
    
    private $pickRunWarnings = [];
    
    /**
     * @var Validator
     */
    private $validator;
    
    
    private function getValidator()
    {
        if (!$this->validator) {
            $this->validator = new Validator();
        }
        return $this->validator;
    }
           
    public function getPickingRun(string $csvPath) {
        if (!file_exists($csvPath)) {
            throw new \Exception('CSV file '.$csvPath.' does not exist');
        }
        
        $currentRow = 0;
        $handle = fopen($csvPath, 'r');
        while (($row = fgetcsv($handle)) !== false) {
            $currentRow++;
            if ($currentRow == 1) {
                $this->validateHeaderRow($row);
            } else {
                // Data rows. Still needs validation, however we will 
                // discard invalid rows instead of terminating the process
                $this->addItemToPickRun($row);
            }
        }
        fclose($handle);
        
        // Sorting the pick run by bay and shelf
        ksort($this->pickRun);
        
        return $this->pickRun;
    }
    
    /**
     * @param array $row
     * @throws \InvalidArgumentException
     */
    private function validateHeaderRow(array $row) {
        $validateResult = $this->getValidator()->validateInputCsvHeaderRow($row);
        if (!$validateResult->isValid()) {
            throw new \InvalidArgumentException(
                'Csv header invalid. Details: '.$validateResult->getDisplayErrorMessage()
            );
        }
    }
    
    /**
     * For efficiency Im choosing to create the pick run as were loading the CSV.
     * So there would be just one iteration and then a sorting by key.
     * @param array $row
     */
    public function addItemToPickRun(array $row) {
        $validateResult = $this->getValidator()->validateInputRow($row);
        
        if (!$validateResult->isValid()) {    
            $warningMessage = 'Invalid row in input detected. Row: '.var_export($row, true)
                .' Validation error message: '.$validateResult->getDisplayErrorMessage();
            $this->pickRunWarnings[] = $warningMessage;
            return False;
        }
        
        // The row has been validated. Now we can get the key and add it to the pick run
        $productCode = $row[0];
        $quantity = intval($row[1]);
        $pickLocation = $row[2];
        $locationDetails = $this->getBayAndShelfFromPickLocation($pickLocation);
        $bay = $locationDetails['bay'];
        $shelf = $locationDetails['shelf'];
        $key = $this->getItemKey($bay, $shelf, $productCode);
        
        if (key_exists($key, $this->pickRun)) {
            $this->pickRun[$key]['quantity'] += $quantity;
        } else {
            $this->pickRun[$key] = [
                'productCode' => $productCode,
                'quantity' => $quantity,
                'pickLocation' => $pickLocation,
            ];
        }
        return True;
    }
    
    private function getBayAndShelfFromPickLocation($pickLocation) {
        $locationParts = explode(' ', $pickLocation);
        return ['bay' => $locationParts[0], 'shelf' => $locationParts[1]];
    }
    
    
    /**
     * @param string $bay
     * @param string $shelf
     * @param string $productCode
     * @return string
     */
    private function getItemKey(string $bay, string $shelf, string $productCode)
    {
        // For product code doing 10 here to make sure there potential for growth
        return str_pad($bay, 2, 'A', STR_PAD_LEFT).'_'
            .str_pad($shelf, 2, '0', STR_PAD_LEFT).'_'
            .str_pad($productCode, 10, '0', STR_PAD_LEFT);
    }
    
    /**
     * 
     * @param string $csvPath
     * @param array $pickRun
     * @return boolean
     * @throws \Exception
     */
    public function writeCsv(string $csvPath, array $pickRun) {
        if (file_exists($csvPath))
        {
            throw new \Exception('Output CSV file already exists. Please delete it and run again.');
        }
        
        $fp = fopen($csvPath, 'w');
        fputcsv($fp, ['product_code', 'quantity', 'pick_location']);
        
        foreach ($pickRun as $row) {
            fputcsv($fp, $row);
        }
        
        fclose($fp);
        return True;
    }
    
    /**
     * Warnings during parsing rows
     * @return array
     */
    public function getPickRunWarnings() {
        return $this->pickRunWarnings;
    }
    
}
