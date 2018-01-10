<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Import;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Import\Importer;
use Avro\CsvBundle\Util\Reader;

/**
 * Test importer class.
 */
class ImporterTest extends \PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        $fields = ['id', 'field1', 'field2'];
        $this->fields = $fields;
        $caseConverter = new CaseConverter();
        $reader = new Reader();
        $metadata = $this->getMockForAbstractClass('Doctrine\Common\Persistence\Mapping\ClassMetadata', ['hasField']);
        $metadata->expects($this->any())
            ->method('hasField')
            ->will($this->returnCallback(function ($value) use ($fields) {
                return in_array($value, $fields);
            }));
        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectManager->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($metadata));
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->any())
            ->method('dispatch')
            ->will($this->returnValue('true'));

        $this->importer = new Importer($reader, $dispatcher, $caseConverter, $objectManager, 5);

        $this->importer->init(__DIR__.'/../import.csv', 'Avro\CsvBundle\Tests\TestEntity', ',', 'title');
    }

    /**
     * Test import.
     */
    public function testImport()
    {
        $this->assertEquals(
            true,
            $this->importer->import($this->fields)
        );

        $this->assertEquals(
            3,
            $this->importer->getImportCount()
        );
    }
}
