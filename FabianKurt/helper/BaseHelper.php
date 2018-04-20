<?php

namespace helper;

class BaseHelper
{

    protected $renderer;
    public function __construct(\Viewrenderer $renderer)
    {
        $this->renderer = $renderer;
    }
}