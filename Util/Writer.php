<?php

namespace Avro\CsvBundle\Util;

/*
 * Creates a CSV file from an array
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Writer
{
    protected $handle;
    protected $delimiter;
    protected $enclosure;
    protected $line;
    protected $headers;

    /*
     * Open CSV file
     *
     * @param $file 
     * @param $mode
     * @param string $delimiter 
     * @param string $enclosure
     */
    public function open($file, $delimiter = ',', $mode = 'r+', $enclosure = '"')
    {
        file_put_contents($file);
        $this->handle = fopen($file, $mode);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->line = 0;
    }

    /*
     * Convert to CSV row
     *
     * @param string or array $row The data to convert
     * @param boolean Adds linebreak if true
     */
    public function convertRow($row, $addBreak = true)
    {
        $formatValue = function($value) {
            if ($value instanceof \Datetime) {
                $value = date_format($value, 'Y-m-d');
            }

            return trim($value);
        };

        if (is_array($row)) {
            $row = array_map($formatValue, $row);
        } else {
            $row = explode(',', $row);
            $row = array_map('trim', $row);
        }
        if ($addBreak) {
            $row = <<<EOT
$row

EOT;
        }

        return $row;
    }

    /*
     * Write a row in the CSV file
     *
     * @param string or array $row The data to add to the CSV
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
