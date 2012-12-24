<?php

namespace Avro\CsvBundle\Tests;

class TestEntity
{
    protected $id;

    protected $field1;

    protected $field2;

    protected $field3;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getField1()
    {
        return $this->field1;
    }
    public function setField1($field1)
    {
        $this->field1 = $field1;
    }
}
