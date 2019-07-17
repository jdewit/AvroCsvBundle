<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Tests;

class TestEntity
{
    protected $id;

    protected $field1;

    protected $field2;

    protected $assoc;

    protected $custom;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getField1()
    {
        return $this->field1;
    }

    public function setField1($field1): void
    {
        $this->field1 = $field1;
    }

    public function getField2()
    {
        return $this->field2;
    }

    public function setField2($field2): void
    {
        $this->field2 = $field2;
    }

    public function getAssoc()
    {
        return $this->assoc;
    }

    public function setAssoc($assoc): void
    {
        $this->assoc = $assoc;
    }

    public function getCustom()
    {
        return $this->custom;
    }

    public function setCustom($custom): void
    {
        $this->custom = $custom;
    }
}
