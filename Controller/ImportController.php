<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;

use Avro\CsvBundle\Form\Type\ImportFormType;

/**
 * Csv Import controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ImportController extends ContainerAware
{
    /**
     * Upload a csv.
     *
     * @param string $alias The objects alias
     *
     * @return View
     */
    public function uploadAction($alias)
    {
        $fieldChoices = $this->container->get('avro_csv.field_retriever')->getFields($this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)), 'title', true);

        $form = $this->container->get('form.factory')->create(new ImportFormType(), null, array('field_choices' => $fieldChoices));

        return $this->container->get('templating')->renderResponse('AvroCsvBundle:Import:upload.html.twig', array(
            'form' => $form->createView(),
            'alias' => $alias
        ));
    }

    /**
     * Move the csv file to a temp dir and get the user to map the fields.
     *
     * @param Request $request The request
     * @param string  $alias   The objects alias
     *
     * @return view
     */
    public function mappingAction(Request $request, $alias)
    {
        $fieldChoices = $this->container->get('avro_csv.field_retriever')->getFields($this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)), 'title', true);

        $form = $this->container->get('form.factory')->create(new ImportFormType(), null, array('field_choices' => $fieldChoices));

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $reader = $this->container->get('avro_csv.reader');

                $file = $form['file']->getData();
                $filename = $file->getFilename();

                $tmpUploadDir = $this->container->getParameter('avro_csv.tmp_upload_dir');

                $file->move($tmpUploadDir);

                $reader->open(sprintf('%s%s', $tmpUploadDir, $filename), $form['delimiter']->getData());

                $headers = $this->container->get('avro_case.converter')->toTitleCase($reader->getHeaders());
                $row = $reader->getRow();

                return $this->container->get('templating')->renderResponse('AvroCsvBundle:Import:mapping.html.twig', array(
                    'form' => $form->createView(),
                    'alias' => $alias,
                    'headers' => $headers,
                    'headersJson' => json_encode($headers, JSON_FORCE_OBJECT),
                    'row' => $row
                ));
            }
        } else {
            return new RedirectResponse($this->container->get('router')->generate($this->container->getParameter('avro_csv.objects.%s.redirect_route', $alias)));
        }
    }

    /**
     * Previews the uploaded csv and allows the user to map the fields.
     *
     * @param Request $request The request
     * @param string  $alias   The objects alias
     *
     * @return view
     */
    public function processAction(Request $request, $alias)
    {
        $fieldChoices = $this->container->get('avro_csv.field_retriever')->getFields($this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)), 'title', true);

        $form = $this->container->get('form.factory')->create(new ImportFormType(), null, array('field_choices' => $fieldChoices));

        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $importer = $this->container->get('avro_csv.importer');

                $importer->init(sprintf('%s%s', $this->container->getParameter('avro_csv.tmp_upload_dir'), $form['filename']->getData()), $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias), $form['delimiter']->getData()));

                $importer->import($form['fields']->getData());

                $this->container->get('session')->getFlashBag()->set('success', $importer->getImportCount().' items imported.');

            } else {
                $this->container->get('session')->getFlashBag()->set('error', 'Import failed. Please try again.');
            }
        }

        return new RedirectResponse($this->container->get('router')->generate($this->container->getParameter(sprintf('avro_csv.objects.%s.redirect_route', $alias))));
    }
}
