<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Export;

use Doctrine\Orm\Query;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Import csv to doctrine entity/document
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
abstract class Exporter
{
    protected $queryBuilder;

    /**
     * Export all of an objects data to csv format
     *
     * @return $content
     */
    public function getContent()
    {
        $iteratableResults = $this->queryBuilder->getQuery()->iterate(null, 2);

        $content = null;
        foreach ($iteratableResults as $row) {
            $row = reset($row);

            if ($content == null) {
                $content = $this->arrayToCsv(array_keys($row));
            }
            $content .= $this->arrayToCsv(array_values($row));
        }

        return $content;
    }

    /**
      * Converts an array into a CSV string.
      *
      * @param array  $fields    The php array to convert
      * @param string $delimiter The CSV delimiter
      * @param string $enclosure The CSV enclosure
      *
      * @return string CSV formatted string
      */
    public function arrayToCsv(array $fields, $delimiter = ',', $enclosure = '"')
    {
        $output = array();
        foreach ($fields as $field) {
            if ($field instanceof \Datetime) { // format datetime fields
                $field = date_format($field, 'Y-m-d');
            }

            $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        }

        return implode($delimiter, $output) . "\n";
    }

    /**
     * Get the queryBuilder
     *
     * @return QueryBuilder $queryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }
}
