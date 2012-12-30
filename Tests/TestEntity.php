<?php

namespace Avro\CsvBundle\Tests;

class TestEntity
{
    protected $id;

    protected $field1;

    protected $field2;

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

    public function getField2()
    {
        return $this->field2;
    }
    public function setField2($field2)
    {
        $this->field2 = $field2;
    }

}
