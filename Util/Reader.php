<?php
namespace Avro\CsvBundle\Util;

/*
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Reader 
{
    /*
     * Parse csv file into array
     *
     * @param $file 
     * @param $delimiter
     * @param $mode
     *
     * @return array 
     */
    public function parse($file, $delimiter = ",", $mode = "r") {
        $handle = fopen($file, $mode);
         
        $result = array();
        while (($data = fgetcsv($handle, 5000, $delimiter)) !== FALSE) {
            $result[] = $data;
        }

        return $result;
    }
}
