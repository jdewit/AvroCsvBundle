<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Controller;

use Avro\CsvBundle\AvroCsvEvents;
use Avro\CsvBundle\Event\ExportedEvent;
use Avro\CsvBundle\Event\ExportEvent;
use Avro\CsvBundle\Export\ExporterInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * CSV Export controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ExportController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private $exporter;
    private $eventDispatcher;

    /**
     * ExportController constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param ExporterInterface        $exporter
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ExporterInterface $exporter)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->exporter = $exporter;
    }

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

        $this->exporter->init($class);

        $this->eventDispatcher->dispatch(AvroCsvEvents::EXPORT, new ExportEvent($this->exporter));

        $content = $this->exporter->getContent();

        $this->eventDispatcher->dispatch(AvroCsvEvents::EXPORTED, new ExportedEvent($content));

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s.csv"', $alias));

        return $response;
    }
}
