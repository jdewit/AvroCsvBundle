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

- Import data by csv file
- Export data to csv file
- A few services for reading/writing csv files

Supports
--------
- Doctrine ORM

Installation
------------

This bundle is listed on packagist.

Simply add it to your apps composer.json file

```json
    "avro/csv-bundle": "^0.4.2"
```

Enable the bundle in config/bundles.php as well as the dependent AvroCaseBundle:

```php
    Avro\CsvBundle\AvroCsvBundle::class => ['all' => true],
    Avro\CaseBundle\AvroCaseBundle::class => ['all' => true],
```

Configuration
-------------

Add this required config

```yaml
# config/packages/avro_csv.yaml
avro_csv:
    db_driver: orm # supports orm
    batch_size: 15 # The batch size between flushing & clearing the doctrine object manager
    tmp_upload_dir: "%kernel.root_dir%/../web/uploads/tmp/" # The directory to upload the csv files to
    sample_count: 5 # The number of sample rows to show during mapping
```

Add routes to your config

```yaml
# config/routes/avro_csv.yaml
avro_csv:
    resource: "@AvroCsvBundle/Resources/config/routing.yml"
```

Add the entities/documents you want to implement importing/exporting for

```yaml
# config/packages/avro_csv.yaml
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

Importing
---------

Implement importing for as many entities/documents as you like. All you have to do is 
add them to the objects node as mentioned previously.

Then just include a link to specific import page like so:

```html
<a href="{{ path('avro_csv_import_upload', {'alias': 'client'}) }}">Go to import page</a>
```

Replace "client" with whatever alias you called your entity/document in the config.

Views
-----

The bundle comes with some basic twitter bootstrap views that you can 
override by extending the bundle.

Association mapping
-------------------

An event is fired when importing an association field to allow implementing your
own logic fitting
 
Just create a custom listener in your app that listens for the ``AssociationFieldEvent::class`` event.

A simple implementation getting an associated entity by name could look like:

```php
namespace App\EventListener;

use Avro\CsvBundle\Event\AssociationFieldEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Csv import listener
 */
class ImportListener implements EventSubscriberInterface
{
    private $em;

    /**
     * @param EntityManagerInterface   $em      The entity manager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            AssociationFieldEvent::class => 'importAssociation',
        ];
    }

    /**
     * Set the objects createdBy field
     *
     * @param AssociationFieldEvent $event
     */
    public function importAssociation(AssociationFieldEvent $event)
    {
        $association = $event->getAssociationMapping();
        switch ($association['type']) {
            case ClassMetadataInfo::ONE_TO_ONE:
            case ClassMetadataInfo::MANY_TO_ONE:
                $relation = $this->em->getRepository($association['targetEntity'])->findOneBy(
                    [
                        'name' => $event->getRow()[$event->getIndex()],
                    ]
                );
                if ($relation) {
                    $event->getObject()->{'set'.ucfirst($association['fieldName'])}($relation);
                }
                break;
        }
    }
}
```

Customizing each row
--------------------

Want to customize certain fields on each row? No problem.

An event is fired when a row is added that you can tap into to customize each row of data.

Just create a custom listener in your app that listens for the ``RowAddedEvent::class`` event.

For example...

```php
namespace App\EventListener;

use Avro\CsvBundle\Event\RowAddedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Csv import listener
 */
class ImportListener implements EventSubscriberInterface
{
    private $em;
    private $context;

    /**
     * @param EntityManagerInterface   $em      The entity manager
     * @param SecurityContextInterface $context The security context
     */
    public function __construct(EntityManagerInterface $em, SecurityContextInterface $context)
    {
        $this->em = $em;
        $this->context = $context;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            RowAddedEvent::class => 'setCreatedBy',
        ];
    }

    /**
     * Set the objects createdBy field
     *
     * @param RowAddedEvent $event
     */
    public function setCreatedBy(RowAddedEvent $event)
    {
        $object = $event->getObject();

        $user = $this->context->getToken()->getUser();

        $object->setCreatedBy($user);
    }
}
```

Register your listener or use autowiring

Exporting
---------

This bundle provides some simple exporting functionality. 

Navigating to "/export/your-alias" will export all of your data to a csv and allow 
you to download it from the browser.

You can customize the export query builder and the exported data by listening to the
corresponding events (See events in the ``Avro\CsvBundle\Event`` namespace).

If you want to customize data returned, just create your own controller action and grab 
the queryBuilder from the exporter and add your constraints before calling "getContent()". 

Ex.

```php
namespace App\Controller;

use Avro\CsvBundle\Export\ExporterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends AbstractController
{
    /**
     * Export a db table.
     *
     * @param ExporterInterface $exporter The exporter
     * @param string            $alias    The objects alias
     *
     * @return Response
     */
    public function exportAction(ExporterInterface $exporter, string $alias): Response
    {
        $class = $this->getParameter(sprintf('avro_csv.objects.%s.class', $alias));

        $exporter->init($class);

        // customize the query
        $qb = $exporter->getQueryBuilder();
        $qb->where('o.fieldName =? 1')->setParameter(1, false);

        $content = $exporter->getContent();

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s.csv"', $alias));

        return $response;
    }
}

```

Register your controller or use your already setup autowiring

To Do:
------

- Finish mongodb support

Acknowledgements
----------------

Thanks to jwage's <a href="https://github.com/jwage/EasyCSV">EasyCSV</a> for some ground work.


Feedback and pull requests are much appreciated!
