<?php

namespace Avro\CsvBundle\Tests\Doctrine;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Doctrine\Importer;
use Avro\CsvBundle\Util\Reader;

class ImporterTest extends \PHPUnit_Framework_TestCase
{
    protected $fieldRetriever;
    protected $class;
    protected $importer;

    /**
     * Setup test class.
     */
    public function setUp()
    {
        $caseConverter = new CaseConverter();
        $reader = new Reader();
        $metadata = $this->createMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata
            ->expects($this->any())
            ->method('hasField')
            ->willReturn(true);
        $metadataFactory = $this->createMock('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');
        $metadataFactory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->willReturn($metadata);
        $objectManager = $this->createMock('Doctrine\Common\Persistence\ObjectManager');
        $objectManager
            ->expects($this->any())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory);
        $dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $class = 'Avro\CsvBundle\Tests\TestEntity';

        $this->importer = new Importer($reader, $dispatcher, $caseConverter, $objectManager, 5);
        $this->importer->init(__DIR__.'/../import.csv', $class, ',', 'title');
    }

    /**
     * Test getHeaders.
     */
    public function testGetHeaders()
    {
        $this->assertEquals(
            [
                0 => 'Header 1',
                1 => 'Header 2',
                2 => 'Header 3',
            ],
            $this->importer->getHeaders()
        );
    }
}
