<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Row association event.
 *
 * @author Steffen Roßkamp <steffen.rosskamp@gimmickmedia.de>
 */
class AssociationFieldEvent extends Event
{
    protected $object;
    protected $associationMapping;
    protected $row;
    protected $fields;
    protected $headers;
    protected $index;

    /**
     * @param object $object
     * @param array  $associationMapping
     * @param array  $row
     * @param array  $fields
     * @param array  $headers
     * @param int    $index
     */
    public function __construct(object $object, array $associationMapping, array $row, array $fields, array $headers, int $index)
    {
        $this->object = $object;
        $this->associationMapping = $associationMapping;
        $this->row = $row;
        $this->fields = $fields;
        $this->headers = $headers;
        $this->index = $index;
    }

    /**
     * @return object
     */
    public function getObject(): object
    {
        return $this->object;
    }

    /**
     * Get field association mapping.
     *
     * @return array
     */
    public function getAssociationMapping(): array
    {
        return $this->associationMapping;
    }

    /**
     * Get field row.
     *
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * Get mapped fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get CSV headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the current field index.
     *
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }
}
