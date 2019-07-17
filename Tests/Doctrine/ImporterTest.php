<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Doctrine;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Doctrine\Importer;
use Avro\CsvBundle\Tests\TestEntity;
use Avro\CsvBundle\Util\Reader;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImporterTest extends TestCase
{
    protected $fieldRetriever;
    protected $class;
    /**
     * @var Importer
     */
    protected $importer;

    /**
     * Setup test class.
     */
    public function setUp(): void
    {
        $caseConverter = $this->createMock(CaseConverter::class);
        $caseConverter
            ->method('convert')
            ->willReturn(
                [
                    0 => 'Header 1',
                    1 => 'Header 2',
                    2 => 'Header 3',
                ]
            );
        $reader = new Reader();
        $objectManager = $this->createMock(ObjectManager::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->class = TestEntity::class;

        $this->importer = new Importer($reader, $dispatcher, $caseConverter, $objectManager, 5);
    }

    /**
     * Test getHeaders.
     */
    public function testGetHeaders(): void
    {
        $this->importer->init(__DIR__.'/../import.csv', $this->class);
        $this->assertEquals(
            [
                0 => 'Header 1',
                1 => 'Header 2',
                2 => 'Header 3',
            ],
            $this->importer->getHeaders()
        );
    }

    /**
     * Test getHeaders.
     */
    public function testGetFormHeaders(): void
    {
        $this->importer->init(__DIR__.'/../import.csv', $this->class, ',', 'form');
        $this->assertEquals(
            [
                0 => sha1('Header 1'),
                1 => sha1('Header 2'),
                2 => sha1('Header 3'),
            ],
            $this->importer->getHeaders()
        );
    }
}
