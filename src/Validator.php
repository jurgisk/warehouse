<?php

namespace Warehouse;
use Warehouse\Entity\ValidationResult;

/**
 * Description of Validator
 *
 * @author jurgis
 */
class Validator {
    
    public function validateInputCsvHeaderRow(array $row) {
        $check = [
            'expected number of columns match' => count($row) == 3,
            'collumn 1 correct' => isset($row[0]) && $row[0] == 'product_code',
            'collumn 2 correct' => isset($row[1]) && $row[1] == 'quantity',
            'collumn 3 correct' => isset($row[2]) && $row[2] == 'pick_location',
        ];
        
        $valid = !in_array(False, $check);
        $result = new ValidationResult($valid);
        
        if (!$valid) {
            $result->addErrorMessage(
                    'CSV header row is not valid. '
                    . 'Validation failed on the following checks:'
            );            
            $problemKeys = array_keys($check, false, true);
            foreach ($problemKeys as $problem) {
                $result->addErrorMessage($problem);
            }
            $result->addErrorMessage(
                'CSV header should consist of product_code, quantity, '
                    . 'pick_location'
            );
        }
        
        return $result;
    }
    
    /**
     * Validation on rows are very strict - extra spaces are considered to be errors.
     * Also the pick location X is considered to be invalid and will be discarded.
     * Thats just an approach that I have taken. Could have gone the other way around
     * as well - where I'm prefix X with AX and then the row becomes valid, however
     * that leads to garbage in - good result out, which is probably wrong when 
     * dealing with orders.
     * @param array $row
     * @return ValidationResult
     */
    public function validateInputRow(array $row) {
        
        try {
            $check = [
                'expected number of columns match' => count($row) == 3,
                'product code valid' => isset($row[0]) && $this->isProductCodeValid($row[0]),
                'quantity valid' => isset($row[1]) && $this->isQuantityValid($row[1]),
                'pick location valid' => isset($row[2]) && $this->isPickLocationValid($row[2]),
            ];
            $valid = !in_array(False, $check);
            $result = new ValidationResult($valid);
            if (!$valid) {
                $result->addErrorMessage(
                    'CSV row is not valid. '
                    . 'Validation failed on the following checks:'
                );            
                $problemKeys = array_keys($check, false, true);
                foreach ($problemKeys as $problem) {
                    $result->addErrorMessage($problem);
                }
            }    
        } catch (\Exception $ex) {
            $result = (new ValidationResult(false))
                ->addErrorMessage('Validation failed most likely because of invalid data type in a row')
                ->addErrorMessage('Exception message: '.$ex->getMessage());
        }
        
        return $result;
    }
    
    /**
     * Here I am assuming that product code is a positive integer. Don't know that though.
     * Could very well be 00001 in which case I would need to change the type to 
     * string and alter the validation
     * @param int $productCode
     * @return bool
     */
    public function isProductCodeValid($productCode) {
        return intval($productCode) > 0;
    }
    
    public function isQuantityValid($quantity) {
        return intval($quantity) > 0;
    }
    
    /**
     * For this task Im assuming that only bays A to AZ with shelves 1 to 10 are valid
     * Everything else will be discarded
     * @param string $pickLocation
     * @return boolean
     */
    public function isPickLocationValid(string $pickLocation) {
        $parts = explode(' ', $pickLocation);
        $check = [
            'format correct' => count($parts) == 2,
            'bay correct' => isset($parts[0]) && $this->isBayNameValid($parts[0]),
            'shelve correct' => isset($parts[1]) && $this->isShelfNumberValid($parts[1]),
        ];
        return !in_array(false, $check);
    }
    
    public function isBayNameValid(string $bayName) {
        $lenght = strlen($bayName);
        if ($lenght < 1 || $lenght > 2)
        {
            return false;
        }
        
        if (substr($bayName, 0, 1) !== 'A') {
            return false;
        }
        
        if ($lenght == 1)
        {
            return True;
        }
        
        $secondLetter = substr($bayName, 1, 1);
        return (in_array($secondLetter, range('B', 'Z')));
    }
    
    /**
     * 
     * @param int|str $shelfNumber
     * @return boolean
     */
    public function isShelfNumberValid($shelfNumber)
    {
        $value = intval($shelfNumber);
        return $value >= 1 && $value <= 10;
    }
    
    
}
