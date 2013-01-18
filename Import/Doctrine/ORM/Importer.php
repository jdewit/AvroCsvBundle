<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Import\Doctrine\ORM;

use Avro\CsvBundle\Import\Importer as AbstractImporter;
use Avro\CsvBundle\Event\RowAddedEvent;

/**
 * Import csv to doctrine entity
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Importer extends AbstractImporter
{
    /**
     * Add Csv row to db
     *
     * @param array   $row        An array of data
     * @param array   $fields     An array of the fields to import
     * @param string  $dateFormat Date format
     * @param boolean $andFlush   Flush the ObjectManager
     */
    protected function addRow($row, $fields, $dateFormat, $andFlush = true)
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
            $fieldName = lcfirst($v);
            if ($this->metadata->hasField($fieldName)) {
                $value = $row[$k];
                switch ($this->metadata->getTypeOfField($fieldName)) {
                    case 'datetime':
                        $value = \DateTime::createFromFormat($dateFormat, $row[$k]);
                        break;
                    default:
                        $value = $row[$k];
                        break;
                }
                $entity->{'set'.$fields[$k]}($value);
            } else if ($this->metadata->hasAssociation($fieldName)) {
                $association = $this->metadata->associationMappings[$fieldName];
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

}
