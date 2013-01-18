<?php

namespace Avro\CsvBundle\Tests\Fixtures\ORM;

use \Doctrine\ORM\Mapping as ORM;

use Avro\CsvBundle\Annotation\ImportExclude;

/**
 * Test Entity Fixture
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 *
 * @ORM\Entity
 */
class TestEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $stringField;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     * @ImportExclude
     */
    protected $stringField2;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=100, nullable=true)
     */
    protected $integerField;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateField;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getStringField()
    {
        return $this->stringField;
    }
    public function setStringField($stringField)
    {
        $this->stringField = $stringField;
    }


    public function getStringField2()
    {
        return $this->stringField2;
    }
    public function setStringField2($stringField2)
    {
        $this->stringField2 = $stringField2;
    }

    public function getIntegerField()
    {
        return $this->integerField;
    }
    public function setIntegerField($integerField)
    {
        $this->integerField = $integerField;
    }

    public function getDateField()
    {
        return $this->dateField;
    }

    public function setDateField($dateField)
    {
        $this->dateField = $dateField;
        return $this;
    }
}

