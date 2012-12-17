<?php

namespace Avro\CsvBundle\Tests\Util\ReaderTest;

use Avro\CsvBundle\Util\Reader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    private $_reader;

    public function setUp()
    {
        $this->reader = new Reader();
        $this->reader->open(__DIR__ . '/../import.csv');
    }

    public function testGetHeaders()
    {
        $this->assertEquals(
            array(
                0 => 'Column1',
                1 => 'Column2',
                2 => 'Column3',
            ),
            $this->reader->getHeaders()
        );
    }

    public function testGetRow()
    {
        $this->assertEquals(
            array(
                0 => '1column2value',
                1 => '1column3value',
                2 => '1column4value',
            ),
            $this->reader->getRow()
        );
    }

    public function testGetAll()
    {
        $this->assertEquals(5, count($this->reader->getAll()));
    }
}
