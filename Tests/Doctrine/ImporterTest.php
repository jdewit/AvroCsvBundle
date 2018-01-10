<?php

namespace Avro\CsvBundle\Tests\Doctrine;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Doctrine\Importer;
use Avro\CsvBundle\Util\Reader;
use Doctrine\Common\Annotations\AnnotationReader;

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
        $metadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata->expects($this->any())
            ->method('hasField')
            ->will($this->returnValue(true));
        $metadataFactory = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');
        $metadataFactory->expects($this->any())
            ->method('getMetadataFor')
			->will($this->returnValue($metadata));
        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectManager->expects($this->any())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metadataFactory));
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

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
