<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Doctrine;

use Avro\CsvBundle\Import\Importer as BaseImporter;

/**
 * Import csv to doctrine entity/document.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Importer extends BaseImporter
{
    /**
     * Get the csv's header row.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the csv's next row.
     *
     * @return array
     */
    public function getRow()
    {
        return $this->reader->getRow();
    }
}
