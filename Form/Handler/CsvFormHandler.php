<?php
namespace Avro\CsvBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Avro\CsvBundle\Annotation\ExcludeImport;

/*
 * Csv Form Handler
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CsvFormHandler
{
    protected $form;
    protected $request;
    protected $clientManager;
    protected $annotationReader;
    protected $csvReader;
    protected $em;
    protected $metadata;
    protected $class;
    protected $headers;
    protected $batchSize;
    protected $context;
    protected $useOwner;
    protected $importCount = 0;

    public function __construct(Form $form, Request $request, $annotationReader, $csvReader, $em, $batchSize, $context, $useOwner)
    {
        $this->form = $form;
        $this->request = $request;  
        $this->annotationReader = $annotationReader;
        $this->csvReader = $csvReader;
        $this->em = $em;
        $this->batchSize = $batchSize;
        $this->context = $context;
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
                //$fields = $this->getFieldsFromMetadata($metadata);

                $i = 0;
                while ($row = $this->csvReader->getRow()) {
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
        $reflectionClass = new \ReflectionClass($this->class);
        foreach ($reflectionClass->getProperties() as $property) {
            foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Exclude) {
                    continue;
                }

                // Create new entity
                $entity = new $this->class();
                $fieldName = $property->getName();

                if ($fieldName == 'id') {
                    continue;
                }

                if ($this->metadata->hasAssociation($fieldName)) {
                    $association = $this->metadata->associationMappings[$fieldName];
                    switch ($association['type']) {
                        case '1': // oneToOne
                            //Todo:
                        break;
                        case '2': // manyToOne
                            $joinColumnId = $association['joinColumns'][0]['name'];
                            $legacyId = $row[array_search($joinColumnId, $this->headers)];
                            if ($legacyId) {
                                try {
                                    $criteria = array('legacyId' => $legacyId);
                                    if ($this->useOwner) {
                                        $criteria['owner'] = $this->context->getToken()->getUser()->getOwner();
                                    }
                                    $relation = $this->em->getRepository($association['targetEntity'])->findOneBy($criteria);
                                    if ($relation) {
                                        $entity->{'set'.ucFirst($association['fieldName'])}($relation);
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
                } else {
                    $key = array_search($fieldName, $this->headers);
                    if ($key) {
                        if (array_key_exists($key, $row)) {
                            $entity->{'set'.ucFirst($fieldName)}($row[$key]);
                        } 
                    }
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
