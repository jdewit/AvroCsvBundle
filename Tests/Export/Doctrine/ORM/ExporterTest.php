<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Export\Doctrine\ORM;

use Avro\CsvBundle\Export\Doctrine\ORM\Exporter;
use Doctrine\ORM\QueryBuilder;

/**
 * Test exporter class.
 */
class ExporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Exporter
     */
    protected $exporter;

    public function setUp()
    {
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
            ->disableOriginalConstructor()
            ->setMethods(['iterate', 'HYDRATE_ARRAY', 'getSQL', '_doExecute'])
            ->getMock();
        $query->expects($this->any())
            ->method('iterate')
            ->willReturn([0 => [0 => ['row 1' => 'val\'1', 'row 2' => 'val,2', 'row 3' => 'val"3']]]);
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['select', 'from', 'getQuery'])
            ->getMock();
        $queryBuilder->expects($this->any())
            ->method('select')
            ->willReturn($queryBuilder);
        $queryBuilder->expects($this->any())
            ->method('from')
            ->willReturn($queryBuilder);
        $queryBuilder->expects($this->any())
            ->method('from')
            ->willReturn($queryBuilder);
        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['createQueryBuilder'])
            ->getMock();
        $entityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $this->exporter = new Exporter($entityManager);
    }

    /**
     * Test init.
     */
    public function testInit()
    {
        $this->exporter->init('Avro\CsvBundle\Tests\TestEntity');
        $this->assertTrue($this->exporter->getQueryBuilder() instanceof QueryBuilder);
    }

    /**
     * Test convert row.
     */
    public function testArrayToCsv()
    {
        $this->assertEquals(
            '"val\'1","val,2","val""3"' . "\n",
            $this->exporter->arrayToCsv(['val\'1', 'val,2', 'val"3'])
        );
    }

    /**
     * Test convert row.
     */
    public function testGetContent()
    {
        $expected = '"row 1","row 2","row 3"';
        $expected .= "\n";
        $expected .= '"val\'1","val,2","val""3"';
        $expected .= "\n";

        $this->exporter->init('Avro\CsvBundle\Tests\TestEntity');
        $this->assertEquals(
            $expected,
            $this->exporter->getContent()
        );
    }
}
