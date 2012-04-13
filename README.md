AvroCsvBundle
=============

A simple service for importing and exporting data with CSV files.


Configuration
=============

``` yml
avro_csv:
    import:
        batch_size: 15 #number of rows you want to flush at a time
        legacy_id: false #whether to set a legacy id on the entity. Useful for keeping relations in tact
        use_owner: false #only applicable if your entities have an owner relation
```

Usage
=====

### Importing

Add an import action to your controller.

``` php
    /**
     *  Import clients via csv.
     *
     * @Route("/import", name="avro_crm_client_import")
     * @Template
     */
    public function importAction()
    {
        $form = $this->container->get('avro_csv.form');
        $formHandler = $this->container->get('avro_csv.form.handler');

        $process = $formHandler->process('Avro\CrmBundle\Entity\Client');
        if ($process === true) {
            $this->container->get('session')->getFlashBag()->set('success', $formHandler->getImportCount().' clients imported.');

            return new RedirectResponse($this->container->get('router')->generate('avro_crm_client_list'));
        } 

        return array(
            'form' => $form->createView()
        );
    }

```

### Exporting

Add an export action to your controller. This is the easiest way 
without actually having to create the file.

``php 
    /**
     *  Export clients via csv.
     *
     * @Route("/export", name="avro_crm_client_export")
     * @Template
     */
    public function exportAction()
    {
        $clientManager = $this->container->get('avro_crm.client_manager');
        $writer = $this->container->get('avro_csv.writer');
        $selected = $request->request->get('selected'); // an array of ids
        foreach ($selected as $id) {
            $client = $clientManager->findAsArray($id); // find the entity in array format
            if ($id === reset($selected)) {
                $content = $writer->convertRow(array_keys($client));
            }

            $content .= $writer->convertRow(array_values($client)); // build the csv file
        }

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="clients.csv"');

        return $response; 
    }
```

Installation
============

Add the `Avro` namespace to your autoloader:

``` php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Avro' => __DIR__.'/../vendor/bundles',
));
```

Enable the bundle in the kernel:

``` php
// app/AppKernel.php

    new Avro\CsvBundle\AvroCsvBundle
```

```
[AvroCsvBundle]
    git=git://github.com/jdewit/AvroCsvBundle.git
    target=bundles/Avro/CsvBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

Acknowledgements
================

Thanks to Jonathan Wage's <a href="https://github.com/jwage/EasyCSV.git">EasyCsvBundle</a> for inspiration.
