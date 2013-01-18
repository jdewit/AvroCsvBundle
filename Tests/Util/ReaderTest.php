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
                0 => 'Id',
                1 => 'String Field',
                2 => 'Integer Field',
                3 => 'Date Field',
            ),
            $this->reader->getHeaders()
        );
    }

    public function testGetRow()
    {
        $this->assertEquals(
            array(
                0 => '1',
                1 => 'string 1',
                2 => '11',
                3 => '2012-01-01'
            ),
            $this->reader->getRow()
        );
        $this->assertEquals(
            array(
                0 => '5',
                1 => 'string 2',
                2 => '22',
                3 => '2012-02-02'
            ),
            $this->reader->getRow()
        );
        $this->assertEquals(
            array(
                0 => '10',
                1 => 'string 3',
                2 => '33',
                3 => '2012-03-03'
            ),
            $this->reader->getRow()
        );
    }

    public function testGetRows()
    {
        $this->assertEquals(
            array(
                0 => array(
                    0 => '1',
                    1 => 'string 1',
                    2 => '11',
                    3 => '2012-01-01'
                ),
                1 => array(
                    0 => '5',
                    1 => 'string 2',
                    2 => '22',
                    3 => '2012-02-02'
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
