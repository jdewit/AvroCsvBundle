<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Row added event.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class RowAddedEvent extends Event
{
    protected $object;
    protected $row;
    protected $fields;

    /**
     * @param object $object The new object being persisted
     * @param array  $row    The row being imported
     * @param array  $fields The mapped fields
     */
    public function __construct(object $object, array $row, array $fields)
    {
        $this->object = $object;
        $this->row = $row;
        $this->fields = $fields;
    }

    public function setObject(?object $object): void
    {
        $this->object = $object;
    }

    /**
     * Get the doctrine object.
     */
    public function getObject(): ?object
    {
        return $this->object;
    }

    /**
     * Get field row.
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * Get mapped fields.
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
