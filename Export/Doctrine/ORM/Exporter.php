<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Export\Doctrine\ORM;

use Avro\CaseBundle\Util\Converter;

use Avro\CsvBundle\Export\Exporter as BaseExporter;
use Avro\CsvBundle\Export\ExporterInterface;

use Doctrine\ORM\EntityManager;

/**
 * Import csv to doctrine entity/document
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Exporter extends BaseExporter implements ExporterInterface
{
    protected $entityManager;

    /**
     * @param entityManager $entityManager The csv entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Initialize the exporter by setting the queryBuilder
     *
     * @param string $class The fully qualified class name
     */
    public function init($class)
    {
        $this->queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from($class, 'o');
    }
}
