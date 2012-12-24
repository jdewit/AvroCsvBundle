<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Row added event
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class RowAddedEvent extends Event
{
    protected $object;
    protected $row;
    protected $fields;

    /**
     * @param DoctrineObject $object The new object being persisted
     * @param array          $row    The row being imported
     * @param array          $fields The mapped fields
     */
    public function __construct($object, array $row, array $fields)
    {
        $this->object = $object;
        $this->row = $row;
        $this->fields = $fields;
    }

    /**
     * Get the doctrine object
     *
     * @return DoctrienObject
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Get field row
     *
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Get mapped fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}

