<?php

namespace models;


class Tag extends Entity
{
    private $id;
    public $tagname;






    protected function defaultValidationConfiguration()
    {
        $this->validator->isRequired('tagname');
        $this->validator->minLength('tagname',1);
    }


    public function save()
    {
        return$this->queryBuilder->setMode(2)->setColsWithValues("tags",array('id','tagname'),array('',$this->tagname))->executeStatement();
    }

}