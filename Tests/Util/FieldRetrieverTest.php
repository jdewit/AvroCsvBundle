<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Util;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Util\FieldRetriever;
use Doctrine\Common\Annotations\AnnotationReader;

class FieldRetrieverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldRetriever
     */
    protected $fieldRetriever;
    /**
     * @var string
     */
    protected $class;

    public function setUp()
    {
        $annotationReader = new AnnotationReader();
        $caseConverter = new CaseConverter();
        $this->fieldRetriever = new FieldRetriever($annotationReader, $caseConverter);
        $this->class = 'Avro\CsvBundle\Tests\TestEntity';
    }

    public function testGetFields()
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class),
            array(
                '0' => 'Id',
                '1' => 'Field1',
                '2' => 'Field2',
            )
        );
    }

    public function testGetFieldsAsCamelCase()
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class, 'camel'),
            array(
                '0' => 'id',
                '1' => 'field1',
                '2' => 'field2',
            )
        );
    }

    public function testGetFieldsAndCopyKeys()
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class, 'camel', true),
            array(
                'id' => 'id',
                'field1' => 'field1',
                'field2' => 'field2',
            )
        );
    }
}
