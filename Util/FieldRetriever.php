<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Util;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Annotation\ImportExclude;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Retrieves the fields of a Doctrine entity/document that
 * are allowed to be imported.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
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
     * Get the entity/documents field names.
     *
     * @param string $class     The class name
     * @param string $format    The desired field case format
     * @param bool   $copyToKey Copy the field values to their respective key
     *
     * @return array $fields
     */
    public function getFields($class, $format = 'title', $copyToKey = false)
    {
        $reflectionClass = new \ReflectionClass($class);
        $properties = $reflectionClass->getProperties();

        $fields = [];
        foreach ($properties as $property) {
            $addField = true;
            foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof ImportExclude) {
                    $addField = false;
                }
            }

            if ($addField) {
                $fields[] = $this->caseConverter->convert($property->getName(), $format);
            }
        }
        // Add empty field so fields can be skipped
        array_unshift($fields, '');

        if ($copyToKey) {
            $fields = array_combine($fields, $fields);
        }

        return $fields;
    }
}
