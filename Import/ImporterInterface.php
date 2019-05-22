<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Import;

use Doctrine\ORM\Mapping\MappingException;

/**
 * Import csv to doctrine entity/document.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
interface ImporterInterface
{
    /**
     * Import a file.
     *
     * @param string $file         The csv file
     * @param string $class        The class name of the entity
     * @param string $delimiter    The csv's delimiter
     * @param string $headerFormat The header case format
     */
    public function init($file, $class, $delimiter = ',', $headerFormat = 'title'): void;

    /**
     * Import the csv and persist to database.
     *
     * @param array $fields The fields to persist
     *
     * @throws MappingException
     */
    public function import($fields): void;

    /**
     * Converts a string to a format suitable as form name.
     *
     * @param string|array $input
     *
     * @return string|array
     */
    public function toFormFieldName($input);

    /**
     * Get import count.
     *
     * @return int
     */
    public function getImportCount(): int;

    /**
     * Get import errors.
     *
     * @return int
     */
    public function getImportErrors(): int;
}
