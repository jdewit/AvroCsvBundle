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

    /**
     * Setup test class
     */
    public function setUp()
    {
        $annotationReader = new AnnotationReader();
        $caseConverter = new CaseConverter();
        $reader = new Reader();
        $metadataFactory = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');
        $metadataFactory->expects($this->any())
            ->method('getMetadataFor')
            ->will($this->returnValue(array(
                'fieldMappings' => array(
                    '0' => 'id'
                )
            )));

        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectManager->expects($this->any())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metadataFactory));
        $objectManager->expects($this->any())
            ->method('hasField')
            ->will($this->returnValue(true));

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $class = 'Avro\CsvBundle\Tests\TestEntity';

        $this->importer = new Importer($reader, $dispatcher, $caseConverter, $objectManager, 5);
        $this->importer->init(__DIR__ . '/../import.csv', $class, ',', 'title');
    }

    /**
     * Test getHeaders
     */
    public function testGetHeaders()
    {
        $this->assertEquals(
            array(
                0 => 'Header 1',
                1 => 'Header 2',
                2 => 'Header 3',
            ),
            $this->importer->getHeaders()
        );
    }


}
