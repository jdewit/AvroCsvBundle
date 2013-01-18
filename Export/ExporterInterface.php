<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Export;

/**
 * Exporter interface
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
interface ExporterInterface
{
    /**
     * Initialize the exporter by setting the queryBuilder
     *
     * @param string $class The fully qualified class name
     */
    public function init($class);

    /**
     * Export all of an objects data to csv format
     *
     * @return $content
     */
    public function getContent();

    /**
      * Converts an array into a CSV string.
      *
      * @param array  $fields    The php array to convert
      * @param string $delimiter The CSV delimiter
      * @param string $enclosure The CSV enclosure
      *
      * @return string CSV formatted string
      */
    public function arrayToCsv(array $fields, $delimiter = ',', $enclosure = '"');

}


