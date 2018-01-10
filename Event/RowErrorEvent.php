<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Row error event.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class RowErrorEvent extends Event
{
    protected $row;
    protected $fields;

    /**
     * @param array $row    The row being imported
     * @param array $fields The mapped fields
     */
    public function __construct(array $row, array $fields)
    {
        $this->row = $row;
        $this->fields = $fields;
    }

    /**
     * Get field row.
     *
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Get mapped fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
