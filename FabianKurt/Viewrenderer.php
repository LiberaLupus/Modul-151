<?php

use helper\FormHelper;
use helper\HTMLHelper;

require_once("Autoloader.php");

class Viewrenderer
{

    public $formHelper;
    public $htmlHelper;
    public $queryBuilder;
    public $controller;
    public $sessionManager;
    private $viewVars = ['image'=>'/assets/images/','assets'=>'/assets/'];

    public $headerIndex = 0;


    public function __construct($controller)
    {

        $this->formHelper = new FormHelper($this);
        $this->htmlHelper = new HTMLHelper($this);
        $this->controller=$controller;
        $this->queryBuilder = new \services\QueryBuilder();
        $this->sessionManager = new\services\SessionManager();
        $this->setAttribute('image',"/".basename(__DIR__).$this->image);
        $this->setAttribute('assets',"/".basename(__DIR__).$this->assets);
    }


    public function setAttribute($key, $value)
    {
        $this->viewVars[$key] = $value;

    }

    private function startCapturing()
    {
        ob_start();
    }

    private function endCapturing(): string
    {
        $content = ob_get_contents();
        ob_flush();
        return $content;
    }


    public function getFileContent(string $path)
    {
        if (file_exists($path)) {
            include($path);
        }
    }

    private function getPath(string $fileName, string $defaultPath = "/view/"): string
    {
        return __DIR__ . $defaultPath . "/" . $fileName;
    }

    public function renderLayout(string $layoutName)
    {
        $this->renderByFileName('/layout/' . $layoutName);
    }

    public function renderByFileName(string $fileName)
    {
        $this->test;
        $this->startCapturing();
        $path = $this->getPath($fileName);
        $this->getFileContent($path);
        $string = $this->endCapturing();
    }

    public function __get($param)
    {
        if (isset($this->viewVars[$param])) {
            return $this->viewVars[$param];
        }
    }

}