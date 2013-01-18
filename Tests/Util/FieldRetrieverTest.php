<?php

namespace Avro\CsvBundle\Tests\Util;

use Avro\CsvBundle\Util\FieldRetriever;
use Avro\CaseBundle\Util\CaseConverter;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;

class FieldRetrieverTest extends \PHPUnit_Framework_TestCase
{
    protected $fieldRetriever;
    protected $class;

    public function setUp()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
        AnnotationRegistry::registerFile(__DIR__ . '/../../Annotation/ImportExclude.php');

        $annotationReader = new AnnotationReader();
        $caseConverter = new CaseConverter();
        $this->fieldRetriever = new FieldRetriever($annotationReader, $caseConverter);
        $this->class = 'Avro\CsvBundle\Tests\Fixtures\ORM\TestEntity';
    }

    public function testGetFields()
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class),
            array(
                '0' => 'Id',
                '1' => 'String Field',
                '2' => 'Integer Field',
                '3' => 'Date Field',
            )
        );
    }

    public function testGetFieldsAsCamelCase()
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class, 'camel'),
            array(
                '0' => 'id',
                '1' => 'stringField',
                '2' => 'integerField',
                '3' => 'dateField',
            )
        );
    }

    public function testGetFieldsAndCopyKeys()
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class, 'camel', true),
            array(
                'id' => 'id',
                'stringField' => 'stringField',
                'integerField' => 'integerField',
                'dateField' => 'dateField',
            )
        );
    }

}
