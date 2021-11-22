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

    public function __construct(object $object, array $associationMapping, array $row, array $fields, array $headers, int $index)
    {
        $this->object = $object;
        $this->associationMapping = $associationMapping;
        $this->row = $row;
        $this->fields = $fields;
        $this->headers = $headers;
        $this->index = $index;
    }

    public function getObject(): object
    {
        return $this->object;
    }

    /**
     * Get field association mapping.
     */
    public function getAssociationMapping(): array
    {
        return $this->associationMapping;
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

    /**
     * Get CSV headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the current field index.
     */
    public function getIndex(): int
    {
        return $this->index;
    }
}
