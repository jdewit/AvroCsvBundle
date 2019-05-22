<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Export\Doctrine\ORM;

use Avro\CsvBundle\Export\Doctrine\ORM\Exporter;
use Avro\CsvBundle\Tests\TestEntity;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Test exporter class.
 */
class ExporterTest extends TestCase
{
    /**
     * @var Exporter
     */
    protected $exporter;

    public function setUp()
    {
        $query = $this->getMockForAbstractClass(AbstractQuery::class, [], '', false, true, true, ['iterate', 'HYDRATE_ARRAY', 'getSQL', '_doExecute']);
        $query
            ->method('iterate')
            ->willReturn([0 => [0 => ['row 1' => 'val\'1', 'row 2' => 'val,2', 'row 3' => 'val"3']]]);
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['select', 'from', 'getQuery'])
            ->getMock();
        $queryBuilder
            ->method('select')
            ->willReturn($queryBuilder);
        $queryBuilder
            ->method('from')
            ->willReturn($queryBuilder);
        $queryBuilder
            ->method('from')
            ->willReturn($queryBuilder);
        $queryBuilder
            ->method('getQuery')
            ->willReturn($query);
        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['createQueryBuilder'])
            ->getMock();
        $entityManager
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $this->exporter = new Exporter($entityManager);
    }

    /**
     * Test init.
     */
    public function testInit()
    {
        $this->exporter->init(TestEntity::class);
        $this->assertInstanceOf(QueryBuilder::class, $this->exporter->getQueryBuilder());
    }

    /**
     * Test convert row.
     */
    public function testArrayToCsv()
    {
        $this->assertEquals(
            '"val\'1","val,2","val""3"'."\n",
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

        $this->exporter->init(TestEntity::class);
        $this->assertEquals(
            $expected,
            $this->exporter->getContent()
        );
    }
}
