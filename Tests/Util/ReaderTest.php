<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Util;

use Avro\CsvBundle\Util\Reader;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    /**
     * @var Reader
     */
    protected $reader;

    public function setUp(): void
    {
        $this->reader = new Reader();
        $this->reader->open(__DIR__.'/../import.csv');
    }

    public function testGetHeaders(): void
    {
        $this->assertEquals(
            [
                0 => 'Header 1',
                1 => 'Header 2',
                2 => 'Header 3',
            ],
            $this->reader->getHeaders()
        );
    }

    public function testGetRow(): void
    {
        $this->assertEquals(
            [
                0 => 'row1column1',
                1 => 'row1column2',
                2 => 'row1column3',
            ],
            $this->reader->getRow()
        );
        $this->assertEquals(
            [
                0 => 'row2column1',
                1 => 'row2column2',
                2 => 'row2column3',
            ],
            $this->reader->getRow()
        );
        $this->assertEquals(
            [
                0 => 'row3column1',
                1 => 'row3column2',
                2 => 'row3column3',
            ],
            $this->reader->getRow()
        );
    }

    public function testGetRows(): void
    {
        $this->assertEquals(
            [
                0 => [
                    0 => 'row1column1',
                    1 => 'row1column2',
                    2 => 'row1column3',
                ],
                1 => [
                    0 => 'row2column1',
                    1 => 'row2column2',
                    2 => 'row2column3',
                ],
            ],
            $this->reader->getRows(2)
        );
    }

    public function testGetAll(): void
    {
        $this->assertCount(3, $this->reader->getAll());
    }
}
