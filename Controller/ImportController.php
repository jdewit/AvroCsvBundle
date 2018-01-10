<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Controller;

use Avro\CsvBundle\Form\Type\ImportFormType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Csv Import controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ImportController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Upload a csv.
     *
     * @param string $alias The objects alias
     *
     * @return Response
     */
    public function uploadAction($alias)
    {
        $fieldChoices = $this->container->get('avro_csv.field_retriever')->getFields($this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)), 'title', true);

        $form = $this->container->get('form.factory')->create(ImportFormType::class, null, ['field_choices' => $fieldChoices]);

        return $this->container->get('templating')->renderResponse('@AvroCsvBundle/Import/upload.html.twig', [
            'form' => $form->createView(),
            'alias' => $alias,
        ]);
    }

    /**
     * Move the csv file to a temp dir and get the user to map the fields.
     *
     * @param Request $request The request
     * @param string  $alias   The objects alias
     *
     * @return Response
     */
    public function mappingAction(Request $request, $alias)
    {
        $fieldChoices = $this->container->get('avro_csv.field_retriever')->getFields($this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)), 'title', true);

        $form = $this->container->get('form.factory')->create(ImportFormType::class, null, ['field_choices' => $fieldChoices]);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $reader = $this->container->get('avro_csv.reader');

                $file = $form['file']->getData();
                $filename = $file->getFilename();

                $tmpUploadDir = $this->container->getParameter('avro_csv.tmp_upload_dir');

                $file->move($tmpUploadDir);

                $reader->open(sprintf('%s%s', $tmpUploadDir, $filename), $form['delimiter']->getData());

                $fileHeaders = $reader->getHeaders();
                $headers = $this->container->get('avro_csv.importer')->toFormFieldName($fileHeaders);

                // Recreate form and create proper fields child for each header
                $form = $this->container->get('form.factory')->create(ImportFormType::class, null, ['field_choices' => $fieldChoices]);
                $form->get('fields')->setData(array_fill_keys((array) $headers, null));
                $form->handleRequest($request);

                $rows = $reader->getRows($this->container->getParameter('avro_csv.sample_count'));

                return $this->container->get('templating')->renderResponse('@AvroCsvBundle/Import/mapping.html.twig', [
                    'form' => $form->createView(),
                    'alias' => $alias,
                    'headers' => array_combine((array) $headers, (array) $fileHeaders),
                    'headersJson' => json_encode($this->container->get('avro_case.converter')->toTitleCase($fileHeaders), JSON_FORCE_OBJECT),
                    'rows' => $rows,
                ]);
            }
        } else {
            return new RedirectResponse($this->container->get('router')->generate($this->container->getParameter(sprintf('avro_csv.objects.%s.redirect_route', $alias))));
        }
    }

    /**
     * Previews the uploaded csv and allows the user to map the fields.
     *
     * @param Request $request The request
     * @param string  $alias   The objects alias
     *
     * @return Response
     */
    public function processAction(Request $request, $alias)
    {
        $fieldChoices = $this->container->get('avro_csv.field_retriever')->getFields($this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)), 'title', true);

        $form = $this->container->get('form.factory')->create(ImportFormType::class, null, ['field_choices' => $fieldChoices]);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $importer = $this->container->get('avro_csv.importer');

                $importer->init(
                    sprintf(
                        '%s%s',
                        $this->container->getParameter('avro_csv.tmp_upload_dir'),
                        $form['filename']->getData()
                    ),
                    $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)),
                    $form['delimiter']->getData(),
                    'form'
                );

                $importer->import($form['fields']->getData());

                $this->container->get('session')->getFlashBag()->set('success', $importer->getImportCount().' items imported. '.$importer->getImportErrors().' errors.');
            } else {
                $this->container->get('session')->getFlashBag()->set('error', 'Import failed. Please try again.');
            }
        }

        return new RedirectResponse($this->container->get('router')->generate($this->container->getParameter(sprintf('avro_csv.objects.%s.redirect_route', $alias))));
    }
}
