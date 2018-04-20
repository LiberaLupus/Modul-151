<?php

namespace models;


class Comment extends Entity
{

    public $id;
    public $userFk;
    public $blogFk;
    public $content;

    protected function defaultValidationConfiguration()
    {

        $this->validator->isRequired('content');
    }

}