<?php

namespace Avro\CsvBundle\Util;

use Avro\CsvBundle\Annotation\Exclude;
use Avro\CaseBundle\Util\CaseConverter;

/**
 * Retrieves the fields of a Doctrine entity/document that
 * are allowed to be imported
 */
class FieldRetriever
{
    protected $annotationReader;
    protected $caseConverter;

    /**
     * @param AnnotationReader $annotationReader The annotation reader service
     * @param CaseConverter    $caseConverter    The caseConverter service
     */
    public function __construct($annotationReader, CaseConverter $caseConverter)
    {
        $this->annotationReader = $annotationReader;
        $this->caseConverter = $caseConverter;
    }

    /**
     * Get the entity/documents field names
     *
     * @param string $class The class name
     *
     * @return array $fields
     */
    public function getFields($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $properties = $reflectionClass->getProperties();

        $fields = array();
        foreach ($properties as $property) {
            $addField = true;

            foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Exclude) {
                    $addField = false;
                }
            }

            if ($addField) {
                $fields[] = $this->caseConverter->toTitleCase($property->getName());
            }
        }

        return $fields;
    }
}
