<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Util;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Tests\TestEntity;
use Avro\CsvBundle\Util\FieldRetriever;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FieldRetrieverTest extends TestCase
{
    /**
     * @var FieldRetriever
     */
    protected $fieldRetriever;
    /**
     * @var string
     */
    protected $class;
    /**
     * @var CaseConverter|MockObject
     */
    private $caseConverter;

    public function setUp(): void
    {
        $annotationReader = new AnnotationReader();
        $this->caseConverter = $this->createMock(CaseConverter::class);
        $this->caseConverter
            ->method('convert')
            ->willReturnMap(
                [
                    ['', 'title', ''],
                    ['id', 'title', 'Id'],
                    ['field1', 'title', 'Field1'],
                    ['field2', 'title', 'Field2'],
                    ['assoc', 'title', 'Assoc'],
                    ['custom', 'title', 'Custom'],
                    ['', 'camel', ''],
                    ['id', 'camel', 'id'],
                    ['field1', 'camel', 'field1'],
                    ['field2', 'camel', 'field2'],
                    ['assoc', 'camel', 'assoc'],
                    ['custom', 'camel', 'custom'],
                ]
            );
        $this->fieldRetriever = new FieldRetriever($annotationReader, $this->caseConverter);
        $this->class = TestEntity::class;
    }

    public function testGetFields(): void
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class),
            [
                '0' => '',
                '1' => 'Id',
                '2' => 'Field1',
                '3' => 'Field2',
                '4' => 'Assoc',
                '5' => 'Custom',
            ]
        );
    }

    public function testGetFieldsAsCamelCase(): void
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class, 'camel'),
            [
                '0' => '',
                '1' => 'id',
                '2' => 'field1',
                '3' => 'field2',
                '4' => 'assoc',
                '5' => 'custom',
            ]
        );
    }

    public function testGetFieldsAndCopyKeys(): void
    {
        $this->assertEquals(
            $this->fieldRetriever->getFields($this->class, 'camel', true),
            [
                '' => '',
                'id' => 'id',
                'field1' => 'field1',
                'field2' => 'field2',
                'assoc' => 'assoc',
                'custom' => 'custom',
            ]
        );
    }
}
