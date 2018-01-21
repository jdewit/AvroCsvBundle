<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Util;

/**
 * Creates a CSV file from an array.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Writer
{
    /**
     * @var resource
     */
    protected $handle;
    /**
     * @var string
     */
    protected $delimiter;
    /**
     * @var string
     */
    protected $enclosure;
    /**
     * @var int
     */
    protected $line;

    /**
     * Open CSV file.
     *
     * @param $file
     * @param $mode
     * @param string $delimiter
     * @param string $enclosure
     */
    public function open($file, $delimiter = ',', $mode = 'r+', $enclosure = '"')
    {
        file_put_contents($file, '');
        $this->handle = fopen($file, $mode);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->line = 0;
    }

    /**
     * Convert array to CSV row.
     *
     * @param array $row      The data to convert
     * @param bool  $addBreak Adds linebreak if true
     *
     * @return array
     */
    public function convertRow(array $row, $addBreak = true)
    {
        $formatValue = function ($value) {
            if ($value instanceof \Datetime) {
                $value = date_format($value, 'Y-m-d');
            }

            return trim($value);
        };

        $row = implode(array_map($formatValue, $row), ',');

        if ($addBreak) {
            $row = <<<EOT

$row

EOT;
        }

        return $row;
    }

    /**
     * Write a row in the CSV file.
     *
     * @param string|array $row The data to add to the CSV
     *
     * @return array
     */
    public function writeRow($row)
    {
        $row = $this->convertRow($row, false);

        return fputcsv($this->handle, $row, $this->delimiter, $this->enclosure);
    }

    /*
     * Write an arrow of data to the CSV file
     *
     * @param array $array An array of data to write
     */
    public function writeFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->writeRow($value);
        }
    }

    /*
     * Close file if necessary
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
}
