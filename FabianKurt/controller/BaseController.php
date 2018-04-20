<?php


namespace controller;



use services\DatabaseSeed;

class BaseController
{
    protected $renderer;
    public $httpHandler;
    public $viewTemplate;
    protected $queryResult;
    public $controllerName;
    protected static $dontRender;
    private static $alerts;

    public function getQueryResult(){
        return$this->queryResult;
    }


    public function __construct()
    {
        $this->renderer = new \Viewrenderer($this);
        $this->httpHandler  = new HttpHandler();
        $this->renderer->sessionManager->startSession();
        $this::$dontRender = false;
    }

    public function getController(){
        return$this;
    }

    public function __destruct()
    {
        if (!BaseController::$dontRender){
            //$this->setAlerts();
            $this->renderer->renderLayout('header.php');
            $this->renderer->renderByFileName("/view/" .$this->controllerName . "/" . $this->viewTemplate);
            $this->renderer->renderLayout('footer.php');
        }

    }


    public function createAlert(string $title,string $content,bool $good){
        BaseController::$dontRender=true;
        $good = ($good==true || $good==1)? 'true' : 'false';
        $temp=array('alert'=>true,'title'=>$title,'content'=>$content,'good'=>$good);
        BaseController::$alerts=$temp;
        $this->renderer->sessionManager->setSessionArray('alert',$temp);

    }


    public function resetDatabase(){

        if (($this->renderer->sessionManager->isSet('User') && $this->renderer->sessionManager->getSessionItem('User', 'RoleName') == 'Admin') || isset($_GET['bypass'])){
            $dbseed = new DatabaseSeed();
            $dbseed->resetDatabase();
        }
        $this->httpHandler->redirect('video', 'index');
    }

    public function about()
    {
        $this->renderer->headerIndex = 1;
    }

    public function contact (){
        $this->renderer->headerIndex = 4;
    }


    public function partners(){
        $this->renderer->headerIndex = 3;
    }


    public function filter($mode, $val){
        $this::$dontRender = true;
        $val = '%'.$val.'%';
        switch($mode){
            case 1:
                $res = $this->renderer->queryBuilder->setMode(0)->setTable('product')
                    ->setCols('product', array('id', 'productname', 'image', 'description'))
                    ->joinTable('product_tag', 'product', 0, 'productfk', true)
                    ->joinTable('tags', 'product_tag', 0, 'tagsfk')
                    ->addCond('tags', 'tagname', 6, $val, false)
                    ->groupBy(array('product_tag.productfk'))
                    ->orderBy(array('product.id'))
                    ->executeStatement();
                break;
            case 2:
                $res = $this->renderer->queryBuilder->setMode(0)->setTable('product')
                    ->setCols('product', array('id', 'productname', 'image', 'description'))
                    ->joinTable('dbuser', 'product', 0, 'dbuserfk')
                    ->addCond('dbuser', 'username', 6, $val, false)
                    ->executeStatement();
                break;
            case 3:
                $res = $this->renderer->queryBuilder->setMode(0)->setTable('product')
                    ->setCols('product', array('id', 'productname', 'image', 'description'))
                    ->addCond('product', 'productname', 6, $val, false)
                    ->executeStatement();
                break;
            default:
                $res = $this->renderer->queryBuilder->setMode(0)->setTable('product')->setCols('product', array('id', 'productname', 'image', 'description'))->executeStatement();
                break;
        }

        echo json_encode($res);
        die();
    }

}