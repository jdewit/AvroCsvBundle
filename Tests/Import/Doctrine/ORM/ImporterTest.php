<?php

namespace Avro\CsvBundle\Tests\Import\Doctrine\ORM;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Import\Doctrine\ORM\Importer;
use Avro\CsvBundle\Util\Reader;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Test importer class
 */
class ImporterTest extends \PHPUnit_Framework_TestCase
{
    protected $fieldRetriever;
    protected $class;
    protected $fields;

    /**
     * Setup test class
     *
     * @return nothing
     */
    public function setUp()
    {
        $this->fields = $fields = array('id', 'stringField', 'integerField', 'dateField');

        $caseConverter = new CaseConverter();
        $reader = new Reader();

        $metadata = $this->getMockForAbstractClass('Doctrine\Common\Persistence\Mapping\ClassMetadata', array('hasField'));
        $metadata->expects($this->atLeastOnce())
            ->method('hasField')
            ->will($this->returnCallback(function($value) use ($fields){
                return in_array($value, $fields);
            }));

        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectManager->expects($this->atLeastOnce())
            ->method('getClassMetadata')
            ->will($this->returnValue($metadata));

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->atLeastOnce())
            ->method('dispatch')
            ->will($this->returnValue('true'));

        $this->importer = new Importer($reader, $dispatcher, $caseConverter, $objectManager, 5);

        $this->importer->init(__DIR__ . '/../../../import.csv', 'Avro\CsvBundle\Tests\Fixtures\ORM\TestEntity');
    }

    /**
     * Test import
     */
    public function testImport()
    {
        $this->assertEquals(
            true,
            $this->importer->import($this->fields)
        );
    }

    /**
     * Test number of row imported
     */
    public function testImportCount()
    {
        $this->importer->import($this->fields);

        $this->assertEquals(
            3,
            $this->importer->getImportCount()
        );
    }
}
