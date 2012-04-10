<?php

namespace Avro\CsvBundle\Util;

/*
 * Creates a CSV file from an array
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Writer
{
    /*
     * Open CSV file
     *
     * @param $file 
     * @param $mode
     * @param string $delimiter 
     * @param string $enclosure
     */
    public function open($file, $delimiter = ',', $mode = 'r', $enclosure = '"')
    {
        $this->handle = fopen($file, $mode);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->line = 0;
    }

    public function writeRow($row)
    {
        if (is_string($row)) {
            $row = explode(',', $row);
            $row = array_map('trim', $row);
        }
        return fputcsv($this->handle, $row, $this->delimiter, $this->enclosure);
    }

    public function writeFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->writeRow($value);
        }
    }

    /*
     * Close file
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

}
