<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Controller;

use Avro\CaseBundle\Util\CaseConverter;
use Avro\CsvBundle\Form\Type\ImportFormType;
use Avro\CsvBundle\Import\ImporterInterface;
use Avro\CsvBundle\Util\FieldRetriever;
use Avro\CsvBundle\Util\Reader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Csv Import controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ImportController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private $fieldRetriever;
    private $formFactory;
    private $twig;
    private $router;
    private $caseConverter;
    private $reader;
    private $importer;

    public function __construct(
        FieldRetriever $fieldRetriever,
        FormFactoryInterface $formFactory,
        Environment $twig,
        RouterInterface $router,
        CaseConverter $caseConverter,
        Reader $reader,
        ImporterInterface $importer
    ) {
        $this->fieldRetriever = $fieldRetriever;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->router = $router;
        $this->caseConverter = $caseConverter;
        $this->reader = $reader;
        $this->importer = $importer;
    }

    /**
     * Upload a csv.
     *
     * @param string $alias The objects alias
     *
     * @return Response
     */
    public function uploadAction($alias)
    {
        $fieldChoices = $this->fieldRetriever->getFields(
            $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)),
            'title',
            true
        );
        $form = $this->formFactory->create(ImportFormType::class, null, ['field_choices' => $fieldChoices]);

        return new Response(
            $this->twig->render(
                '@AvroCsv/Import/upload.html.twig',
                [
                    'form' => $form->createView(),
                    'alias' => $alias,
                ]
            )
        );
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
        $fieldChoices = $this->fieldRetriever->getFields(
            $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)),
            'title',
            true
        );

        $form = $this->formFactory->create(ImportFormType::class, null, ['field_choices' => $fieldChoices]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $file = $form['file']->getData();
                $filename = $file->getFilename();

                $tmpUploadDir = $this->container->getParameter('avro_csv.tmp_upload_dir');

                $file->move($tmpUploadDir);

                $this->reader->open(sprintf('%s%s', $tmpUploadDir, $filename), $form['delimiter']->getData());

                $fileHeaders = $this->reader->getHeaders();
                $headers = $this->importer->toFormFieldName($fileHeaders);

                // Recreate form and create proper fields child for each header
                $form = $this->formFactory->create(ImportFormType::class, null, ['field_choices' => $fieldChoices]);
                $form->get('fields')->setData(array_fill_keys((array) $headers, null));
                $form->handleRequest($request);

                $rows = $this->reader->getRows($this->container->getParameter('avro_csv.sample_count'));

                return new Response(
                    $this->twig->render(
                        '@AvroCsv/Import/mapping.html.twig',
                        [
                            'form' => $form->createView(),
                            'alias' => $alias,
                            'headers' => array_combine((array) $headers, (array) $fileHeaders),
                            'headersJson' => json_encode(
                                $this->caseConverter->toTitleCase($fileHeaders),
                                JSON_FORCE_OBJECT
                            ),
                            'rows' => $rows,
                        ]
                    )
                );
            }
        } else {
            return new RedirectResponse(
                $this->router->generate(
                    $this->container->getParameter(sprintf('avro_csv.objects.%s.redirect_route', $alias))
                )
            );
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
        $fieldChoices = $this->fieldRetriever->getFields(
            $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)),
            'title',
            true
        );

        $form = $this->formFactory->create(ImportFormType::class, null, ['field_choices' => $fieldChoices]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->importer->init(
                    sprintf(
                        '%s%s',
                        $this->container->getParameter('avro_csv.tmp_upload_dir'),
                        $form['filename']->getData()
                    ),
                    $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias)),
                    $form['delimiter']->getData(),
                    'form'
                );

                $this->importer->import($form['fields']->getData());

                $request->getSession()->getFlashBag()->set(
                    'success',
                    $this->importer->getImportCount().' items imported. '.$this->importer->getImportErrors().' errors.'
                );
            } else {
                $request->getSession()->getFlashBag()->set('error', 'Import failed. Please try again.');
            }
        }

        return new RedirectResponse(
            $this->router->generate(
                $this->container->getParameter(sprintf('avro_csv.objects.%s.redirect_route', $alias))
            )
        );
    }
}
