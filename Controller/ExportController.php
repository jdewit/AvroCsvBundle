<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Controller;

use Doctrine\Orm\Query;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;

use Avro\CsvBundle\Form\Type\ImportFormType;

/**
 * Csv Export controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ExportController extends ContainerAware
{
    /**
     * Export a db table.
     *
     * @param string $alias The objects alias
     *
     * @return View
     */
    public function exportAction($alias)
    {
        $class = $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias));

        $exporter = $this->container->get('avro_csv.exporter');
        $exporter->init($class);

        $content = $exporter->getContent();

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s.csv"', $alias));

        return $response;
    }
}

