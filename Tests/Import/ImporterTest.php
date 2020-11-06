<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Import;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Import\Importer;
use Avro\CsvBundle\Tests\TestEntity;
use Avro\CsvBundle\Util\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Test importer class.
 */
class ImporterTest extends TestCase
{
    /**
     * @var string[]
     */
    protected $fields;
    /**
     * @var Importer
     */
    protected $importer;

    /**
     * Setup test class.
     */
    public function setUp(): void
    {
        $fields = ['id', 'field1', 'field2'];
        $assocs = ['assoc'];
        $customs = ['assoc'];
        $this->fields = array_merge($fields, $assocs, $customs);
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
        $caseConverter
            ->method('toPascalCase')
            ->with($this->fields)
            ->willReturn(['Id', 'Field1', 'Field2', 'Assoc']);
        $reader = new Reader();
        $metadata = $this->createMock(ClassMetadataInfo::class);
        $metadata
            ->method('hasField')
            ->willReturnCallback(
                static function ($value) use ($fields) {
                    return in_array($value, $fields, true);
                }
            );
        $metadata
            ->method('hasAssociation')
            ->willReturnCallback(
                static function ($value) use ($assocs) {
                    return in_array($value, $assocs, true);
                }
            );
        $metadata
            ->method('getAssociationMapping')
            ->willReturn([]);
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager
            ->method('getClassMetadata')
            ->willReturn($metadata);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->method('dispatch')
            ->willReturn(new stdClass());

        $this->importer = new Importer($reader, $dispatcher, $caseConverter, $objectManager, 5);
        $this->importer->init(__DIR__.'/../import.csv', TestEntity::class);
    }

    /**
     * Test import.
     *
     * @group legacy
     */
    public function testImport(): void
    {
        $this->importer->import($this->fields);
        $this->assertEquals(
            3,
            $this->importer->getImportCount()
        );
    }
}
