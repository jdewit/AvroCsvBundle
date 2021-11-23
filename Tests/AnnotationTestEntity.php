<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests;

use Avro\CsvBundle\Annotation\ImportExclude;

class AnnotationTestEntity
{
    protected $id;

    /**
     * @ImportExclude
     */
    protected $field1;

    #[ImportExclude]
    protected $field2;

    protected $assoc;

    protected $custom;
}
