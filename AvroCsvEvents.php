<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle;

/**
 * Contains all events thrown in the AvroCsv bundle.
 *
 * @author Steffen Roßkamp <steffen.rosskamp@gimmickmedia.de>
 */
final class AvroCsvEvents
{
    /**
     * The EXPORT event occurs after the exporter has been initialized.
     *
     * This event allows you to alter the exporter or its query builder.
     *
     * @Event("Avro\CsvBundle\Event\ExportEvent")
     */
    const EXPORT = 'avro_csv.exporter_export';
    /**
     * The EXPORTED event occurs after the exporter has generated the content.
     *
     * This event allows you to further process the exported content.
     *
     * @Event("Avro\CsvBundle\Event\ExportedEvent")
     */
    const EXPORTED = 'avro_csv.exporter_exported';
    /**
     * The ROW_ADDED event occurs after the importer has generated the entity.
     *
     * This event allows you to alter or nullify the entity.
     *
     * @Event("Avro\CsvBundle\Event\RowAddedEvent")
     */
    const ROW_ADDED = 'avro_csv.row_added';
    /**
     * The ROW_ERROR event occurs when the importer could not import an row.
     *
     * This event allows you to further process the erroneous row.
     *
     * @Event("Avro\CsvBundle\Event\RowErrorEvent")
     */
    const ROW_ERROR = 'avro_csv.row_error';
}
