<?php

namespace Tests;
use PHPUnit\Framework\TestCase;
use Warehouse\Warehouse;

/**
 * Description of WarehouseTest
 *
 * @author jurgis
 */
class WarehouseTest extends TestCase {
    
    
    /**
     * @var Warehouse
     */
    private $warehouse;
    
    public function setup()
    {
        $this->warehouse = new Warehouse();
    }
    
    public function tearDown() {
        $outputFile = dirname(__FILE__).'/output/output.csv';
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }
    }

    public function testGetPickingRunTotals()
    {
        $csvFile = dirname(__FILE__).'/assets/input1_totalling.csv';
        $pickRun = $this->warehouse->getPickingRun($csvFile);
        
        $this->assertEquals(
            [
                'productCode' => '321',
                'quantity' => 321,
                'pickLocation' => 'AC 5',
            ],
            $pickRun['AC_05_0000000321']
        );
        
        $this->assertEquals(
            [
                'productCode' => '123',
                'quantity' => 123,
                'pickLocation' => 'AC 7',
            ],
            $pickRun['AC_07_0000000123']
        );
    }
    
    public function testGetPickingRunDifferentProductsSameBay()
    {
        $csvFile = dirname(__FILE__).'/assets/input2_different_products_same_bay.csv';
        $pickRun = $this->warehouse->getPickingRun($csvFile);
        
        $this->assertEquals(
            [
              'AB_07_0000011111' => [
                'productCode' => '11111',
                'quantity' => 1,
                'pickLocation' => 'AB 7',
              ],
              'AB_07_0000011112' => 
              [
                'productCode' => '11112',
                'quantity' => 2,
                'pickLocation' => 'AB 7',
              ],
              'AB_07_0000011113' => 
              [
                'productCode' => '11113',
                'quantity' => 3,
                'pickLocation' => 'AB 7',
              ],
              'AB_09_0000022222' => 
              [
                'productCode' => '22222',
                'quantity' => 6,
                'pickLocation' => 'AB 9',
              ],
            ],
            $pickRun
        );
    }
    
    public function testGetPickingRunDifferentBays()
    {
        $csvFile = dirname(__FILE__).'/assets/input3_different_bays.csv';
        $pickRun = $this->warehouse->getPickingRun($csvFile);
        $this->assertEquals(
            [
              'AB_09_0000012456' => [
                'productCode' => '12456',
                'quantity' => 10,
                'pickLocation' => 'AB 9',
              ],
              'AB_10_0000052568' => 
              [
                'productCode' => '52568',
                'quantity' => 7,
                'pickLocation' => 'AB 10',
              ],
              'AZ_10_0000088958' => 
              [
                'productCode' => '88958',
                'quantity' => 4,
                'pickLocation' => 'AZ 10',
              ],
            ],
            $pickRun
        );
    }
    
    public function testWriteCsv()
    {
        $inputFile = dirname(__FILE__).'/assets/input_original.csv';
        $outputFile = dirname(__FILE__).'/output/output.csv';
        $pickRun = $this->warehouse->getPickingRun($inputFile);
        $result = $this->warehouse->writeCsv($outputFile, $pickRun);
        $this->assertTrue($result);
        $this->assertFileExists($outputFile);
        $this->assertGreaterThan(0, $this->warehouse->getPickRunWarnings());
    }
    
}
