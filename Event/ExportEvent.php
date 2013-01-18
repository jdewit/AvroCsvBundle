<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Event;

use Avro\CsvBundle\Export\ExporterInterface;

use Symfony\Component\EventDispatcher\Event;

/**
 * Export initialized event
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ExportEvent extends Event
{
    protected $exporter;

    /**
     * @param Exporter $exporter The Avro Exporter service
     */
    public function __construct(ExporterInterface $exporter)
    {
        $this->exporter = $exporter;
    }

    /**
     * Get the avro exporter
     *
     * @return AvroExporter
     */
    public function getExporter()
    {
        return $this->exporter;
    }

    /**
     * Get the queryBuilder
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->exporter->getQueryBuilder();
    }
}

