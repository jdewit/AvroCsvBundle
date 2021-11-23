<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Annotation;

use Attribute;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ImportExclude
{
}
