<?php
/**
 * ahdfasdf dsaf
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */

namespace Avro\CsvBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Avro\CsvBundle\Form\Type\ImportFormType;
use Avro\CsvBundle\Form\Type\PreviewFormType;

/**
 * Csv Import controller.
 *
 *
 */
class ImportController extends ContainerAware
{
    /**
     *  Upload a csv.
     *
     * @param string $alias The objects alias
     *
     * @Route("/import/{alias}", name="avro_csv_import_upload")
     * @Template
     */
    public function importAction($alias)
    {
        $form = $this->container->get('form.factory')->create(new ImportFormType());

        return array(
            'form' => $form->createView(),
            'alias' => $alias
        );
    }

    /**
     *  Previews the uploaded csv and allows the user to map the fields.
     *
     * @param Request $request The request object
     * @param string  $alias   The objects alias
     *
     * @Route("/import/preview/{alias}", name="avro_csv_import_preview")
     * @Method("POST")
     * @Template
     *
     * @return array View
     */
    public function previewAction(Request $request, $alias)
    {
        $form = $this->container->get('form.factory')->create(new ImportFormType());

        $form->bind($request);
        if (!$form->isValid()) {
            return new RedirectResponse($this->container->get('router')->generate($this->container->getParameter(sprintf('avro_csv.objects.%s.redirect_route', $alias))));
        }

        $file = $form['file']->getData();

        $delimiter = $form['delimiter']->getData();

        $csvReader = $this->container->get('avro_csv.reader');
        $csvReader->open($file, $delimiter);

        $headers = $csvReader->getHeaders();

        $row = $csvReader->getRow();

        $class = $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias));

        $fields = $this->container->get('avro_csv.field_retriever')->getFields($class);

        $form = $this->container->get('form.factory')->create(new PreviewFormType(), null, array('fields' => $fields));
        $form['filePath']->setData($file->getFilename());
        $form['delimiter']->setData($delimiter);

        $file->move($this->container->getParameter('avro_csv.tmp_upload_dir'));

        return array(
            'form' => $form->createView(),
            'alias' => $alias,
            'headers' => $headers,
            'headersJson' => json_encode($headers, JSON_FORCE_OBJECT),
            'row' => $row,
            'rowJson' => json_encode($row, JSON_FORCE_OBJECT),
            'fields' => $fields,
            'fieldsJson' => json_encode($fields, JSON_FORCE_OBJECT),
        );
    }

    /**
     * Process upload.
     *
     * @param string $alias The objects alias
     *
     * @Route("/import/process/{alias}", name="avro_csv_import_process")
     * @Method("POST")
     * @Template
     *
     * @return array View
     */
    public function processAction(Request $request, $alias)
    {

        $class = $this->container->getParameter(sprintf('avro_csv.objects.%s.class', $alias));

        $annotationReader = $this->container->get('annotation_reader');

        $reflectionClass = new \ReflectionClass($class);
        $properties = $reflectionClass->getProperties();

        $fields = array();
        foreach ($properties as $property) {
            foreach ($annotationReader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Exclude) {
                    continue;
                }

                $fields[] = $property->getName();
            }
        }

        $form = $this->container->get('form.factory')->create(new PreviewFormType(), null, array('fields' => $fields));
        $form->bind($request);

        $delimiter = $form['delimiter']->getData();

        $csvReader = $this->container->get('avro_csv.reader');
        $file = $this->container->getParameter('avro_csv.tmp_upload_dir').$form['filePath']->getData();
        $csvReader->open($file, $delimiter);

        $headers = $csvReader->getHeaders();

        $row = $csvReader->getRow();

        ld($form->getData()); exit;

        $formHandler = $this->container->get('avro_csv.form.handler');

        $class = $this->container->getParameter(sprintf('avro_csv.import.object.%s.class', $alias));

        $process = $formHandler->process($class);
        if ($process === true) {
            $this->container->get('session')->getFlashBag()->set('success', $formHandler->getImportCount().' items imported.');

            $route = $this->container->getParameter(sprintf('avro_csv.import.object.%s.redirect_route', $alias));

            return new RedirectResponse($this->container->get('router')->generate($route));
        }

        return array(
            'form' => $form->createView(),
            'alias' => $alias
        );
    }

}
