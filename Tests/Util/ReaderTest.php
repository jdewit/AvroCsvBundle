<?php

namespace Avro\CsvBundle\Tests\Util;

use Avro\CsvBundle\Util\Reader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    protected $reader;

    public function setUp()
    {
        $this->reader = new Reader();
        $this->reader->open(__DIR__ . '/../import.csv');
    }

    public function testGetHeaders()
    {
        $this->assertEquals(
            array(
                0 => 'Header 1',
                1 => 'Header 2',
                2 => 'Header 3',
            ),
            $this->reader->getHeaders()
        );
    }

    public function testGetRow()
    {
        $this->assertEquals(
            array(
                0 => 'row1column1',
                1 => 'row1column2',
                2 => 'row1column3',
            ),
            $this->reader->getRow()
        );
        $this->assertEquals(
            array(
                0 => 'row2column1',
                1 => 'row2column2',
                2 => 'row2column3',
            ),
            $this->reader->getRow()
        );
        $this->assertEquals(
            array(
                0 => 'row3column1',
                1 => 'row3column2',
                2 => 'row3column3',
            ),
            $this->reader->getRow()
        );
    }

    public function testGetRows()
    {
        $this->assertEquals(
            array(
                0 => array(
                    0 => 'row1column1',
                    1 => 'row1column2',
                    2 => 'row1column3',
                ),
                1 => array(
                    0 => 'row2column1',
                    1 => 'row2column2',
                    2 => 'row2column3',
                ),
            ),
            $this->reader->getRows(2)
        );
    }

    public function testGetAll()
    {
        $this->assertEquals(3, count($this->reader->getAll()));
    }
}
