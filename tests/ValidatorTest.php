<?php

namespace Tests;
use PHPUnit\Framework\TestCase;
use Warehouse\Validator;

/**
 * @author jurgis
 */
class ValidatorTest extends TestCase {
    
    
    /**
     * @var Validator
     */
    private $validator;
    
    public function setup() {
        $this->validator = new Validator();
    }

    /**
     * Tests CSV header rows. They must match exactly (3 rows with certain names)
     */
    public function testCsvHeaderRowValidation() {
        $header1 = [''];
        $result1 = $this->validator->validateInputCsvHeaderRow($header1);
        $this->assertFalse($result1->isValid());
        
        $header2 = ['one', 'two', 'three'];
        $result2 = $this->validator->validateInputCsvHeaderRow($header2);
        $this->assertFalse($result2->isValid());
        
        $header3 = ['product_code', 'quantity', 'pick_location', 'extra!'];
        $result3 = $this->validator->validateInputCsvHeaderRow($header3);
        $this->assertFalse($result3->isValid());
        
        $header4 = ['product_code', 'quantity', 'pick_location'];
        $result4 = $this->validator->validateInputCsvHeaderRow($header4);
        $this->assertTrue($result4->isValid());
    }
    
    public function testIsPickLocationValid() {
        
        $location1 = 21;
        $result1 = $this->validator->isPickLocationValid($location1);
        $this->assertFalse($result1);
        
        $location2 = ' a a a a';
        $result2 = $this->validator->isPickLocationValid($location2);
        $this->assertFalse($result2);
        
        $location3 = 'AB 1';
        $result3 = $this->validator->isPickLocationValid($location3);
        $this->assertTrue($result3);
        
        $location4 = 'AA 1';
        $result4 = $this->validator->isPickLocationValid($location4);
        $this->assertFalse($result4);
        
        $location5 = 'A- B';
        $result5 = $this->validator->isPickLocationValid($location5);
        $this->assertFalse($result5);
        
        $location6 = 'AX 101';
        $result6 = $this->validator->isPickLocationValid($location6);
        $this->assertFalse($result6);
        
        $location7 = 'A 5';
        $result7 = $this->validator->isPickLocationValid($location7);
        $this->assertTrue($result7);
        
        $location8 = 'AZ 10';
        $result8 = $this->validator->isPickLocationValid($location8);
        $this->assertTrue($result8);
    }
    
}
