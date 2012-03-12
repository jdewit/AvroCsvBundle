AvroCsvBundle
=============

A simple service layer for parsing and importing CSV files.

Usage
=====

To import data, simply call and process the form like so.
The form handler returns the CSV as an array.

``` php
    /**
     *  Import clients via csv.
     *
     * @Route("/import", name="application_crm_client_import")
     * @Template
     */
    public function importAction()
    {
        $form = $this->container->get('avro_csv.csv.form');
        $formHandler = $this->container->get('avro_csv.csv.form.handler');
        $clientManager = $this->container->get('application_crm.client_manager');

        $results = $formHandler->process();
        if (is_array($results)) {
            $columns = array_shift($results);
            foreach($results as $result) {
                $client = $clientManager->create();
                $client->setFirstName($result[array_search('first_name', $columns)]); 
                $client->setLastName($result[array_search('last_name', $columns)]); 
                
                if (next($results)) {
                    $clientManager->update($client, false);
                } else {
                    $clientManager->update($client, true);
                }
            }
        } 

        return array(
            'form' => $form->createView()
        );
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

    new Avro\EasyCsvBundle\AvroEasyCsvBundle
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

