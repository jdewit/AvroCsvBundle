<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Import;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Event\AssociationFieldEvent;
use Avro\CsvBundle\Event\CustomFieldEvent;
use Avro\CsvBundle\Event\RowAddedEvent;
use Avro\CsvBundle\Event\RowErrorEvent;
use Avro\CsvBundle\Util\Reader;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\MappingException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Import csv to doctrine entity/document.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Importer implements ImporterInterface
{
    /**
     * @var string[]
     */
    protected $headers;
    /**
     * @var string[]
     */
    protected $fields;
    /**
     * @var ClassMetadataInfo
     */
    protected $metadata;
    /**
     * @var Reader
     */
    protected $reader;
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    /**
     * @var int
     */
    protected $batchSize = 20;
    /**
     * @var int
     */
    protected $importCount = 0;
    /**
     * @var int
     */
    protected $importErrors = 0;
    /**
     * @var CaseConverter
     */
    protected $caseConverter;
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var string
     */
    protected $class;

    /**
     * @param Reader                   $reader        The csv reader
     * @param EventDispatcherInterface $dispatcher    The event dispatcher
     * @param CaseConverter            $caseConverter The case Converter
     * @param ObjectManager            $objectManager The Doctrine Object Manager
     * @param int                      $batchSize     The batch size before flushing the om
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
     * Import a file.
     *
     * @param string $file         The csv file
     * @param string $class        The class name of the entity
     * @param string $delimiter    The csv's delimiter
     * @param string $headerFormat The header case format
     */
    public function init($file, $class, $delimiter = ',', $headerFormat = 'title'): void
    {
        $this->reader->open($file, $delimiter);
        $this->class = $class;
        $this->metadata = $this->objectManager->getClassMetadata($class);
        if ('form' === $headerFormat) {
            $this->headers = $this->toFormFieldName($this->reader->getHeaders());
        } else {
            $this->headers = $this->caseConverter->convert($this->reader->getHeaders(), $headerFormat);
        }
    }

    /**
     * Import the csv and persist to database.
     *
     * @param array $fields The fields to persist
     *
     * @throws MappingException
     */
    public function import($fields): void
    {
        $fields = array_values($this->caseConverter->toPascalCase($fields));
        while ($row = $this->reader->getRow()) {
            if (0 !== $this->importCount && 0 === ($this->importCount % $this->batchSize)) {
                $result = $this->addRow($row, $fields, true);
            } else {
                $result = $this->addRow($row, $fields, false);
            }
            if ($result) {
                ++$this->importCount;
            } else {
                ++$this->importErrors;
            }
        }
        // one last flush to make sure no persisted objects get left behind
        $this->objectManager->flush();
    }

    /**
     * Converts a string to a format suitable as form name.
     *
     * @param string|array $input
     *
     * @return string|array
     */
    public function toFormFieldName($input)
    {
        if (is_array($input)) {
            $result = [];
            foreach ($input as $val) {
                $result[] = $this->convertToFormFieldName($val);
            }
        } else {
            $result = $this->convertToFormFieldName($input);
        }

        return $result;
    }

    /**
     * Get import count.
     *
     * @return int
     */
    public function getImportCount(): int
    {
        return $this->importCount;
    }

    /**
     * Get import errors.
     *
     * @return int
     */
    public function getImportErrors(): int
    {
        return $this->importErrors;
    }

    /**
     * Generate a hash string suitable as form field name.
     *
     * @param string $input
     *
     * @return string
     */
    private function convertToFormFieldName($input): string
    {
        return sha1($input);
    }

    /**
     * Add Csv row to db.
     *
     * @param array $row      An array of data
     * @param array $fields   An array of the fields to import
     * @param bool  $andFlush Flush the ObjectManager
     *
     * @throws MappingException
     *
     * @return bool
     */
    private function addRow($row, $fields, $andFlush = true): bool
    {
        // Create new entity
        $entity = new $this->class();
        // Loop through fields and set to row value
        foreach ($fields as $k => $v) {
            if ($this->metadata->hasField(lcfirst($v))) {
                $entity->{'set'.$v}($row[$k]);
            } elseif ($this->metadata->hasAssociation(lcfirst($v))) {
                // Let implementors handle associations to allow complex cases
                $event = new AssociationFieldEvent($entity, $this->metadata->getAssociationMapping(lcfirst($v)), $row, $fields, $this->headers, $k);
                $this->dispatcher->dispatch($event);
            } elseif ($this->metadata->getReflectionClass()->hasProperty(lcfirst($v))) {
                $event = new CustomFieldEvent($entity, $this->metadata->getReflectionClass(), $row, $fields, $this->headers, $k);
                $this->dispatcher->dispatch($event);
            }
        }
        // Allow RowAddedEvent listeners to nullify objects (i.e. when invalid)
        $event = new RowAddedEvent($entity, $row, $fields);
        $this->dispatcher->dispatch($event);
        $entity = $event->getObject();
        if (null === $entity) {
            $this->dispatcher->dispatch(new RowErrorEvent($row, $fields));

            return false;
        }
        $this->objectManager->persist($entity);
        if ($andFlush) {
            $this->objectManager->flush();
        }

        return true;
    }
}
