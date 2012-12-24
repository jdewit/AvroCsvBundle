AvroCsvBundle [![Build Status](https://travis-ci.org/jdewit/AvroCsvBundle.png?branch=master)](https://travis-ci.org/jdewit/AvroCsvBundle)
-------------------

This bundle provides an easy way to upload data to your db using csv files with 
just a few configuration parameters.  

Status
------

This bundle is under development and may break.

Limitations
-----------

This bundle uses php and Doctrine2 and is not your best bet for 
importing gargantuan csv files. Use your databases native importing & exporting 
solutions to skin that cat.  

Features
--------

- Import/export data by csv file
- Map the fields in the csv to the entities/documents fields

Supports
--------
- Doctrine ORM

Installation
------------

This bundle is listed on packagist.

Simply add it to your apps composer.json file

``` js
    "avro/csv-bundle": "*"
```

Enable the bundle in the kernel:

``` php
// app/AppKernel.php

    new Avro\CsvBundle\AvroCsvBundle
```

Configuration
-------------

Add this required config to your app/config/config.yml file

``` yaml
avro_csv:
    batch_size: 15 # The batch size between flushing & clearing the doctrine object manager
    tmp_upload_dir: "%kernel.root_dir%/../web/uploads/tmp/" # The directory to upload the csv files to
```

Add routes to your app/config/routing.yml file

``` yaml
AvroCsvBundle:
    resource: "@AvroCsvBundle/Resources/config/routing.yml"
```

Add the entities/documents you want to implement importing/exporting for

``` yaml
avro_csv:
    # 
    objects: # the entities/documents you want to be able to import/export data with 
        client:
            class: Avro\CrmBundle\Entity\Client # The entity/document class
            redirect_route: avro_crm_client_list # The route to redirect to after import
        invoice:
            class: Avro\CrmBundle\Entity\Invoice
            redirect_route: avro_crm_invoice_list
```

To exclude certain fields from being mapped, use the ImportExclude annotation like so.

```php
namespace Avro\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Avro\CsvBundle\Annotation\ImportExclude;

/**
 * Avro\CrmBundle\Entity\Client
 *
 * @ORM\Entity
 */
class Client
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     * @ImportExclude
     */
    protected $password;

```

Uploading A CSV
---------------

Navigate to the import page by adding a link like so:

``` html
<a href="{{ path('avro_csv_import_upload', {'alias': 'client'}) }}">Go to import page</a>
```

Views
-----

The bundle comes with some basic twitter bootstrap views that you can 
override by extending the bundle.

Customizing each row
--------------------

Want to customize certain fields on each row? No problem.

An event is fired when a row is added that you can tap into to customize each row of data.

Just create a custom listener in your app that listens for the 'avro_csv.row_added' event.

For example...

``` php
<?php
namespace Avro\CrmBundle\Listener;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\Event;

/**
 * Csv import listener
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ImportListener
{
    protected $em;
    protected $context;

    /**
     * @param EntityManager            $em      The entity manager
     * @param SecurityContextInterface $context The security context
     */
    public function __construct(EntityManager $em, SecurityContextInterface $context)
    {
        $this->em = $em;
        $this->context = $context;
    }

    /**
     * Set the objects createdBy field
     *
     * @param Event $event
     */
    public function setCreatedBy(Event $event)
    {
        $object = $event->getObject();

        $user = $this->context->getToken()->getUser();

        $object->setCreatedBy($user);
    }
}
```

Register your listener

``` yaml
services:
    import.listener:
        class: Avro\CrmBundle\Listener\ImportListener
        arguments: ["@doctrine.orm.entity_manager", "@security.context"]
        tags:
            - { name: kernel.event_listener, event: avro_csv.row_added, method: setCreatedBy }
```

To Do:
------

- More unit tests
- Allow association mapping 
- Add mongodb support

Acknowledgements
----------------

Thanks to jwage's <a href="https://github.com/jwage/EasyCSV">EasyCSV</a> for some ground work.

