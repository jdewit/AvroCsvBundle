<?php
namespace Avro\CsvBundle\Util;

use Avro\CaseBundle\Util\CaseConverter;

/**
 * Read a CSV file
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Reader
{
    protected $handle;
    protected $delimiter;
    protected $enclosure;
    protected $line;
    protected $headers;
    protected $lineLength;

    /**
     * Open a CSV file
     *
     * @param string  $file       The file path
     * @param string  $delimiter  The CSV's delimiter
     * @param string  $mode       fopen mode
     * @param string  $enclosure  The enclosure
     * @param boolean $hasHeaders Does the CSV have any headers?
     */
    public function open($file, $delimiter = ',', $mode = 'r+', $enclosure = '"', $hasHeaders = true, $lineLength = 0)
    {
        $this->handle = fopen($file, $mode);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->line = 0;
        $this->lineLength = $lineLength;

        if ($hasHeaders) {
            $this->headers = $this->getRow();
        }
    }

    /**
     * Return a row
     *
     * @return array or false
     */
    public function getRow()
    {
        while (($row = fgetcsv($this->handle, $this->lineLength, $this->delimiter, $this->enclosure)) !== false) {
            $this->line++;
            // a blank line returns array of one null. if found, skip it.
            if ((count($row) == 1) && ($row[0] == NULL))
              continue;

            return $row;
        }
        return false;
    }

    /**
     * Get an array of rows
     *
     * @param int $count The number of rows to return
     *
     * @return array of row
     */
    public function getRows($count)
    {
        $rows = array();
        for ($i=0; $i < $count; $i++) {
            $row = $this->getRow();
            if ($row) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Return entire table
     *
     * @return array $data
     */
    public function getAll()
    {
        $data = array();
        while ($row = $this->getRow()) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Close file
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

}
