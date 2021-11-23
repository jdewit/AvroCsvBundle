<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Util;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Tests\AnnotationTestEntity;
use Avro\CsvBundle\Util\FieldRetriever;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use const PHP_VERSION_ID;

class FieldRetrieverAnnotationTest extends TestCase
{
    /**
     * @var FieldRetriever
     */
    protected $fieldRetriever;
    /**
     * @var string
     */
    protected $class;

    public function setUp(): void
    {
        $annotationReader = new AnnotationReader();
        $caseConverter = $this->createMock(CaseConverter::class);
        $caseConverter
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
        $this->fieldRetriever = new FieldRetriever($annotationReader, $caseConverter);
        $this->class = AnnotationTestEntity::class;
    }

    public function testGetAnnotationFields(): void
    {
        if (PHP_VERSION_ID >= 80000) {
            $this->markTestSkipped('This test requires PHP < 8.0');
        }
        $this->assertEquals(
            [
                '0' => '',
                '1' => 'Id',
                '2' => 'Field2',
                '3' => 'Assoc',
                '4' => 'Custom',
            ],
            $this->fieldRetriever->getFields($this->class)
        );
    }

    public function testGetAttributeFields(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('This test requires PHP >= 8.0');
        }
        $this->assertEquals(
            [
                '0' => '',
                '1' => 'id',
                '2' => 'assoc',
                '3' => 'custom',
            ],
            $this->fieldRetriever->getFields($this->class, 'camel')
        );
    }
}
