<?php
namespace Avro\CsvBundle\Util;

/*
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

    /*
     * Open CSV file
     *
     * @param $file 
     * @param $mode
     * @param string $delimiter 
     * @param string $enclosure
     * @param boolean $hasHeaders
     */
    public function open($file, $delimiter = ',', $mode = 'r', $enclosure = '"', $hasHeaders = true)
    {
        $this->handle = fopen($file, $mode);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->line = 0;

        if ($hasHeaders) {
            $this->headers = $this->getRow();
        }
    }

    /*
     * Return a row
     */
    public function getRow()
    {
        if (($row = fgetcsv($this->handle, 1000, $this->delimiter, $this->enclosure)) !== false) {
            $this->line++;
            
            return $row;
        } else {
            return false;
        }
    }

    /*
     * Return entire table
     *
     * @return array results
     */
    public function getAll()
    {
        $data = array();
        while ($row = $this->getRow()) {
            $data[] = $row;
        }

        return $data;
    }

    /*
     * Get headers
     */
    public function getHeaders() 
    {
        return $this->headers;
    }

    /*
     * Get line
     */
    public function getLine()
    {
        return $this->line;
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
