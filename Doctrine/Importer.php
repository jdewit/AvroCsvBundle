<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Doctrine;


use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Annotation\Exclude;
use Avro\CsvBundle\Event\RowAddedEvent;
use Avro\CsvBundle\Util\Reader;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Import csv to doctrine entity/document
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Importer
{
    protected $fields;
    protected $metadata;
    protected $reader;
    protected $batchSize;
    protected $importCount = 0;
    protected $caseConverter;
    protected $objectManager;

    /**
     * @param CsvReader     $reader        The csv reader
     * @param Dispatcher    $dispatcher    The event dispatcher
     * @param CaseConverter $caseConverter The case Converter
     * @param ObjectManager $objectManager The Doctrine Object Manager
     * @param int           $batchSize     The batch size before flushing & clearing the om
     */
    public function __construct(Reader $reader, EventDispatcherInterface $dispatcher, CaseConverter $caseConverter, ObjectManager $objectManager, $batchSize)
    {
        $this->reader = $reader;
        $this->dispatcher = $dispatcher;
        $this->caseConverter = $caseConverter;
        $this->objectManager = $objectManager;
        $this->batchSize = $batchSize;
    }

    /**
     * Import a file
     *
     * @param File   $file         The csv file
     * @param string $class        The class name of the entity
     * @param string $delimiter    The csv's delimiter
     * @param string $headerFormat The header case format
     *
     * @return boolean true if successful
     */
    public function init($file, $class, $delimiter = ',', $headerFormat = 'title')
    {
        $this->reader->open($file, $delimiter);
        $this->class = $class;
        $this->metadata = $this->objectManager->getMetadataFactory()->getMetadataFor($class);
        $this->headers = $this->caseConverter->convert($this->reader->getHeaders(), $headerFormat);
    }

    /**
     * Get the csv's header row
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the csv's next row
     *
     * @return array
     */
    public function getRow()
    {
        return $this->reader->getRow();
    }

    /**
     * Import the csv and persist to database
     *
     * @param array $fields The fields to persist
     *
     * @return true if successful
     */
    public function import($fields)
    {
        $fields = array_unique($this->caseConverter->toPascalCase($fields));

        while ($row = $this->reader->getRow()) {
            if (($this->importCount % $this->batchSize) == 0) {
                $this->addRow($row, $fields, true);
            } else {
                $this->addRow($row, $fields, false);
            }
            $this->importCount++;
        }

        // one last flush to make sure no persisted objects get left behind
        $this->objectManager->flush();

        return true;
    }

    /**
     * Add Csv row to db
     *
     * @param array   $row      An array of data
     * @param array   $fields   An array of the fields to import
     * @param boolean $andFlush Flush the ObjectManager
     */
    private function addRow($row, $fields, $andFlush = true)
    {
        // Create new entity
        $entity = new $this->class();

        if (in_array('Id', $fields)) {
            $key = array_search('Id', $fields);
            if ($this->metadata->hasField('legacyId')) {
                $entity->setLegacyId($row[$key]);
            }
            unset($fields[$key]);
        }

        // loop through fields and set to row value
        foreach ($fields as $k => $v) {
            if ($this->metadata->hasField(lcfirst($v))) {
                $entity->{'set'.$fields[$k]}($row[$k]);
            } else if ($this->metadata->hasAssociation(lcfirst($v))) {
                $association = $this->metadata->associationMappings[lcfirst($v)];
                switch ($association['type']) {
                    case '1': // oneToOne
                        //Todo:
                        break;
                    case '2': // manyToOne
                        continue;
                        // still needs work
                        $joinColumnId = $association['joinColumns'][0]['name'];
                        $legacyId = $row[array_search($this->caseConverter->toCamelCase($joinColumnId), $this->headers)];
                        if ($legacyId) {
                            try {
                                $criteria = array('legacyId' => $legacyId);
                                if ($this->useOwner) {
                                    $criteria['owner'] = $this->owner->getId();
                                }

                                $associationClass = new \ReflectionClass($association['targetEntity']);
                                if ($associationClass->hasProperty('legacyId')) {
                                    $relation = $this->objectManager->getRepository($association['targetEntity'])->findOneBy($criteria);
                                    if ($relation) {
                                        $entity->{'set'.ucfirst($association['fieldName'])}($relation);
                                    }
                                }
                            } catch(\Exception $e) {
                                // legacyId does not exist
                                // fail silently
                            }
                        }
                        break;
                    case '4': // oneToMany
                        //TODO:
                        break;
                    case '8': // manyToMany
                        //TODO:
                        break;
                }
            }
        }

        $this->dispatcher->dispatch('avro_csv.row_added', new RowAddedEvent($entity, $row, $fields));

        $this->objectManager->persist($entity);

        if ($andFlush) {
            $this->objectManager->flush();
            $this->objectManager->clear($this->class);
        }
    }

    /**
     * Get import count
     *
     * @return int
     */
    public function getImportCount()
    {
        return $this->importCount;
    }
}
