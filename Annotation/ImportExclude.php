<?php

namespace Avro\CsvBundle\Annotation;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","ANNOTATION"})
 */
class ImportExclude implements Annotation
{
}
