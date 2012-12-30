<?php

namespace Avro\CsvBundle\Tests\Export\Doctrine\ORM;

use Avro\CsvBundle\Export\Doctrine\ORM\Exporter;

use Doctrine\ORM\QueryBuilder;

/**
 * Test exporter class
 */
class ExporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup test class
     *
     * @return nothing
     */
    public function setUp()
    {
        $query = $this->getMock('Doctrine\ORM\Query', array('iterate', 'HYDRATE_ARRAY'), array(), '', false);
        $query->expects($this->any())
            ->method('iterate')
            ->will($this->returnValue(array(0 => array(0 => array('row 1' => 'val\'1', 'row 2' => 'val,2', 'row 3' => 'val"3')))));

        $queryBuilder = $this->getMock('Doctrine\ORM\QueryBuilder', array('select', 'from', 'getQuery'), array(), '', false);
        $queryBuilder->expects($this->any())
            ->method('select')
            ->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())
            ->method('from')
            ->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())
            ->method('from')
            ->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($query));
//array(0 => array('field1', 'field2')

        $entityManager = $this->getMock('Doctrine\ORM\EntityManager', array('createQueryBuilder'), array(), '', false);
        $entityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder));

        $this->exporter = new Exporter($entityManager);
    }

    /**
     * Test init
     */
    public function testInit()
    {
        $this->exporter->init('Avro\CsvBundle\Tests\TestEntity');
        $this->assertTrue($this->exporter->getQueryBuilder() instanceof QueryBuilder);
    }

    /**
     * Test convert row
     */
    public function testArrayToCsv()
    {
        $this->assertEquals(
            '"val\'1","val,2","val""3"' . "\n",
            $this->exporter->arrayToCsv(array('val\'1', 'val,2', 'val"3'))
        );
    }

    /**
     * Test convert row
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
