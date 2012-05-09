<?php
namespace Avro\CsvBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Avro\CsvBundle\Annotation\Exclude;

/*
 * Csv Form Handler
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CsvFormHandler
{
    protected $form;
    protected $request;
    protected $annotationReader;
    protected $csvReader;
    protected $em;
    protected $context;
    protected $batchSize;
    protected $useLegacyId;
    protected $useOwner;
    protected $importCount = 0;

    public function __construct(Form $form, Request $request, $annotationReader, $csvReader, $em, $context, $batchSize, $useLegacyId, $useOwner)
    {
        $this->form = $form;
        $this->request = $request;  
        $this->annotationReader = $annotationReader;
        $this->csvReader = $csvReader;
        $this->em = $em;
        $this->batchSize = $batchSize;
        $this->context = $context;
        $this->useLegacyId = $useLegacyId;
        $this->useOwner = $useOwner;
    }

    /*
     * Process the form
     *
     * @param string $class The class name of the entity 
     *
     * @return boolean true if successful
     */
    public function process($class)
    {
        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {

                $file = $this->form['file']->getData();
                $delimiter = $this->form['delimiter']->getData();

                $this->csvReader->open($file, $delimiter);

                $this->headers = $this->csvReader->getHeaders(); 
                $cmf = $this->em->getMetadataFactory();
                $this->metadata = $cmf->getMetadataFor($class);
                $this->class = $class;

                $i = 0;
                while ($row = $this->csvReader->getRow()) {
                    // skip rows that dont have an id
                    if (!$row[0]) {
                        continue;
                    }
                    if (($i % $this->batchSize) == 0) {
                        $this->import($row, true);
                    } else {
                        $this->import($row, false);
                    }
                    ++$i;
                }

                $this->em->flush();

                return true;
            } 
        } 
    }

    /*
     * Add Csv row to db
     */
    public function import($row, $andFlush) 
    {
        // Create new entity
        $entity = new $this->class();
        $reflectionClass = new \ReflectionClass($this->class);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {
                $skipProperty = false;
                if ($annotation instanceof Exclude) {
                    $skipProperty = true;
                }
            }

            if (true === $skipProperty) {
                continue;
            }

            $fieldName = $property->getName();

            // set the entities legacyId 
            if ($fieldName === 'id' && true === $this->useLegacyId && $reflectionClass->hasMethod('setLegacyId')) {
                $entity->setLegacyId($row['id']);
            } 

            // set the entities owner
            if ($this->useOwner && $reflectionClass->hasMethod('setOwner')) {
                $entity->setOwner($this->context->getToken()->getUser()->getOwner());
            }

            if ($this->metadata->hasAssociation($fieldName)) {
                $association = $this->metadata->associationMappings[$fieldName];
                switch ($association['type']) {
                    case '1': // oneToOne
                        //Todo:
                    break;
                    case '2': // manyToOne
                        //Todo:
                        //$joinColumnId = $association['joinColumns'][0]['name'];
                        //$legacyId = $row[array_search($joinColumnId, $this->headers)];
                        //if ($legacyId) {
                        //    try {
                        //        $criteria = array('legacyId' => $legacyId);
                        //        if ($this->useOwner) {
                        //            $criteria['owner'] = $this->context->getToken()->getUser()->getOwner();
                        //        }
                        //        $relation = $this->em->getRepository($association['targetEntity'])->findOneBy($criteria);
                        //        if ($relation) {
                        //            $entity->{'set'.ucFirst($association['fieldName'])}($relation);
                        //        }
                        //    } catch(\Exception $e) {
                        //        // legacyId does not exist
                        //        // fail silently
                        //    }
                        //}
                    break;
                    case '4': // oneToMany
                        //TODO:
                    break;
                    case '8': // manyToMany
                        //TODO:
                    break;
                }
            } else {
                $fieldName = ucFirst($fieldName); 
                $key = array_search($fieldName, $this->headers);
                if ($key && array_key_exists($key, $row)) {
                    $entity->{'set'.$fieldName}($row[$key]);
                }
            }
        }
        $this->em->persist($entity);

        $this->importCount++;

        if ($andFlush) {
            $this->em->flush();
            $this->em->clear($this->class);
        }
    }

    /*
     * Get import count
     */
    public function getImportCount()
    {
        return $this->importCount;
    }
}
