<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests\Doctrine;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Doctrine\Importer;
use Avro\CsvBundle\Tests\TestEntity;
use Avro\CsvBundle\Util\Reader;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImporterTest extends TestCase
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
        $objectManager = $this->createMock(ObjectManager::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $class = TestEntity::class;

        $this->importer = new Importer($reader, $dispatcher, $caseConverter, $objectManager, 5);
        $this->importer->init(__DIR__.'/../import.csv', $class);
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
