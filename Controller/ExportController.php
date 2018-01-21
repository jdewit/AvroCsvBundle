<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Controller;

use Avro\CsvBundle\Event\ExportedEvent;
use Avro\CsvBundle\Event\ExportEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

/**
 * CSV Export controller.
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
     * @return Response
     */
    public function exportAction($alias)
    {
        $class = $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias));

        $exporter = $this->container->get('avro_csv.exporter');
        $exporter->init($class);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('avro_csv.exporter_export', new ExportEvent($exporter));

        $content = $exporter->getContent();

        $dispatcher->dispatch('avro_csv.exporter_exported', new ExportedEvent($content));

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s.csv"', $alias));

        return $response;
    }
}
