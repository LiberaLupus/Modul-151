<?php

namespace controller;


use helper\fileUploader;
use models\Favourite;
use models\Product;
use models\product_tag;
use models\Tag;

class VideoController extends BaseController implements ControllerInterface
{

    public function favourite(int $id){
        $this::$dontRender=true;
        if($this->httpHandler->isGet()){
            $data=$this->httpHandler->getData();
            $data['userfk']=$this->renderer->sessionManager->getSessionItem('User','id');
            $data['productfk']=$id;
            $fav = new Favourite();
            $fav->patchEntity($data);
            echo$fav->change();
        }
    }
    public function favourites(){
        if(!$this->renderer->sessionManager->isSet('User')){
            $this->httpHandler->redirect('video','index');
            return;
        }
        $favs = $this->renderer->queryBuilder->setMode(0)
            ->setTable('favourite')
            ->joinTable('product','favourite',0,'productfk')
            ->addCond('favourite','userfk',0,$this->renderer->sessionManager->getSessionItem('User','id'),false)
            ->executeStatement();
        $this->renderer->setAttribute('favourites',$favs);
    }
    public function removeAllFaves(){
        if($this->renderer->sessionManager->isSet('User'))
            $this->renderer->queryBuilder->setMode(3)
                ->setTable('favourite')
                ->addCond('favourite','userfk',0,$this->renderer->sessionManager->getSessionItem('User','id'),true)
                ->executeStatement();
        $this->httpHandler->redirect('video','favourites');
    }

    public function index()
    {
        $this->renderer->headerIndex = 2;
        $statement=$this->renderer->queryBuilder->setMode(0)->setTable('product')
            ->orderBy(array('views desc'))
            ->limitOffset(6);
        $this->renderer->setAttribute('products',$statement->executeStatement());

        $this->renderer->headerIndex=0;
    }

    public function add()
    {
        $this::$dontRender=true;
        if(!$this->renderer->sessionManager->isSet("User")){
            $this->httpHandler->redirect("video","index");
        }

        if($this->httpHandler->isPost() && isset($_POST['tag1']) && $_POST['tag1'] != "") {
            $data = $this->httpHandler->getData();
            $filename=null;
            $videoname=null;
            if(count($_FILES)>0){
                $fileuploder = new fileUploader();
                $filename = $fileuploder->upload($_FILES['image'],0);
                if(substr(basename($_FILES['video']['name']),strlen(basename($_FILES['video']['name']))-3)=="mp3")
                    $videoname=$fileuploder->upload($_FILES['video'],2);
                else
                $videoname = $fileuploder->upload($_FILES['video'],1);
            }
            $product = new Product();
            $data['userfk']=$this->renderer->sessionManager->getSessionItem("User","id");
            $data['image']=$filename;
            $data['video']=$videoname;
            $product->patchEntity($data);
            if($product->isValid()){
                $newProductId = $product->save();
                for($i = 1; $i <= 5; $i++){
                    $key = 'tag'.$i;
                    if (isset($data[$key])){
                        $tagId = $this->renderer->queryBuilder->setMode(0)->setTable('tags')->addCond('tags', 'tagname', 0, $data[$key], false)->executeStatement();
                        if ($tagId){
                            // tag already exists;
                            $tagId = $tagId[0]['ID'];
                        } else {
                            // create new tag
                            $tag = new Tag();
                            $tag->patchEntity(array('tagname' => $data[$key]));
                            if ($tag->isValid()){
                                $tagId = $tag->save();
                            } else {
                                $tagId = -1;
                            }
                        }
                        if ($tagId > 0){
                            $product_tag = new product_tag();
                            $product_tag->patchEntity(array('tagid' => $tagId, 'productid' => $newProductId));
                            if ($product_tag->isValid()){
                                $product_tag->save();
                            }
                        }
                    } else {
                        break;
                    }
                }
            }

        }
        $this->httpHandler->redirect("video","videos");
    }
    public function view(int $id)
    {
        $this->renderer->headerIndex = 2;
        $this::$dontRender=false;
        $productStatement=$this->renderer->queryBuilder->setMode(0)->setTable('Product')
            ->setCols('Product',array('id','dbuserfk','productname','image','video','views','rating','description'))
            ->setCols('DBUser',array('username'))
            ->joinTable('DBUser','Product',0,'DBUserFK')
            ->addCond('product','id',0,$id,'')->executeStatement();

        $tagStatement=$this->renderer->queryBuilder->setMode(0)->setTable('Product')
            ->setCols('tags', array('id', 'tagname'))
            ->joinTable('product_tag', 'product', '0', 'productfk', true)
            ->joinTable('tags', 'product_tag', '0', 'tagsfk')
            ->addCond('product','id',0,$id,'')->executeStatement();
        $faved=$this->isFaved($id);
        $owner=false;
        if($productStatement[0]['dbuserfk']==$this->renderer->sessionManager->getSessionItem('User','id'))
            $owner=true;
        $this->renderer->setAttribute('key',$this->createKey(15));
        $this->renderer->setAttribute('faved',$faved);
        $this->renderer->setAttribute('owner',$owner);
        $this->renderer->setAttribute('product',$productStatement);
        $this->renderer->setAttribute('tag', $tagStatement);
    }

    public function delete(int $id)
    {
        $this::$dontRender=true;
        if($this->renderer->sessionManager->isSet('User') &&$this->httpHandler->isGet()){
            $vid=$this->renderer->queryBuilder->setMode(0)
                ->setTable('product')
                ->setCols('product',array('image','video'))
                ->addCond('product','id',0,$id,true)
                ->addCond('product','dbuserfk',0,$this->renderer->sessionManager->getSessionItem('User','id'),true)
                ->executeStatement();
            $this->renderer->queryBuilder->setMode(3)
                ->setTable('product')
                ->addCond('product','id',0,$id,true)
                ->addCond('product','dbuserfk',0,$this->renderer->sessionManager->getSessionItem('User','id'),true)
                ->executeStatement();
            //deletes video and thumbnail from filesystem. (should do so, doens't seem to do anything atm. even if enabled...)
            if(true){
                unlink($_SERVER['DOCUMENT_ROOT']."/assets/movies/".$vid['video']);
                unlink($_SERVER['DOCUMENT_ROOT']."/assets/images/products/".$vid['image']);
            }
            echo"deleted";
        }
        else
            echo"error";

    }

    public function edit(int $id)
    {
        $tmp=$this->renderer->queryBuilder->setMode(0)
            ->setTable('product')
            ->setCols('product',array('dbuserfk'))
            ->addCond('product','id',0,$id,true)
            ->executeStatement();

        if($this->httpHandler->isPost() && $this->renderer->sessionManager->isSet('User') && $tmp[0]['dbuserfk']==$this->renderer->sessionManager->getSessionItem('User','id')){
            $data=$this->httpHandler->getData();
            $filename= ($data['originalImage']=='null')? null : $data['originalImage'];
            if(strlen($_FILES['image']['name'])>1){
                $fileuploder = new fileUploader();
                $filename = $fileuploder->upload($_FILES['image'],0);
            }
            $product = new Product();
            $data['id']=$id;
            $data['userfk']=$this->renderer->sessionManager->getSessionItem("User","id");
            $data['image']=$filename;
            $product->patchEntity($data);
            if($product->isValid()){
                $product->edit($id);
                $this->createAlert('Edit successful!','Your video was updated successfully.'/*.$hint*/,true);
                $this->httpHandler->redirect('video','view/'.$id);
            }else{
                $this->createAlert('Edit failed!','Invalid input was given.',false);
                $this->httpHandler->redirect('video','view/'.$id);
            }
        }else{
            $this->createAlert('Edit failed!','Invalid form validity.',false);
            $this->httpHandler->redirect('video','view/'.$id);
        }
    }

    public function update(int $id){
        $this->renderer->setAttribute('productID',$id);
        $product=$this->renderer->queryBuilder->setMode(0)->setTable('Product')
            ->setCols('Product',array('id','dbuserfk','productname','image','video','views','description'))
            ->addCond('Product','id',0,$id,false)
            ->executeStatement();
        if($product[0]['dbuserfk']!=$this->renderer->sessionManager->getSessionItem('User','id')){
            $this->createAlert('Invalid Edit!','You do not own the video you tried to edit!',false);
            $this->httpHandler->redirect('video','products');
            die();
        }
        $this->renderer->setAttribute('product',$product[0]);
    }

    public function upload(){
        $this->renderer->headerIndex = 6;
        if($this->httpHandler->isPost())$this->add();
    }

    public function videos(){
        $this->renderer->headerIndex = 2;
        $statement=$this->renderer->queryBuilder->setMode(0)->setTable('product');
        $this->renderer->setAttribute('products',$statement->executeStatement());
    }


    private function isFaved(int $id){
        $tmp=$this->renderer->queryBuilder->setMode(0)
            ->setTable('favourite')
            ->setCols('favourite',array('id','userfk','productfk'))
            ->addCond('favourite','userfk',0,$this->renderer->sessionManager->getSessionItem('User','id'),true)
            ->addCond('favourite','productfk',0,$id,true)
            ->executeStatement();
        return(count($tmp)==0)? false : true;
    }

    public function addView(int $id,int $userid, string $key){
        $this::$dontRender=true;
        $views=$this->getViews($id);
        $validity=$this->renderer->queryBuilder->setMode(0)
            ->setTable('Codes')
            ->setCols('Codes',array('id','code','valid'))
            ->addCond('Codes','code',0,$key,true)
            ->addCond('Codes','valid',4,time(),true)
            ->executeStatement();
        if((count($validity)!=0)){
            $this->deleteKey($validity[0]['id']);
            $this->renderer->queryBuilder->setMode(1)
                ->setTable('product')
                ->setColsWithValues('product',array('views'),array($views+1))
                ->addCond('product','id',0,$id,true)
                ->executeStatement();
        }
        echo$this->getViews($id);

    }

    private function getViews(int $id){
        $this::$dontRender=true;
        return$this->renderer->queryBuilder->setMode(0)
            ->setCols('product',array('views'))
            ->setTable('product')
            ->addCond('product','id',0,$id,true)
            ->executeStatement()[0]['views'];
    }

    public function likeVideo(int $id, int $userid){

    }

    private function unlikeVideo(int $id, int $userid){

    }


    private function createKey(int $minutesOfValidity){
        $this->cleanCodes();
        $key=uniqid();
        $this->renderer->queryBuilder->setMode(2)
            ->setColsWithValues('Codes',array('id','code','valid'),
                array(null,$key,time()+($minutesOfValidity*60)))
            ->executeStatement();
        return $key;
    }


    public function player(int $id){
        $this::$dontRender=true;
        $video=$this->renderer->queryBuilder->setMode(0)
            ->setCols('product',array('id',''))
            ->addCond('product','id',0,$id,true)
            ->executeStatement();
        $this->renderer->setAttribute('video',$video);
    }


    private function cleanCodes(){
        $this->renderer->queryBuilder->setMode(3)
            ->setTable('Codes')
            ->addCond('Codes','valid',2,time(),false)
            ->executeStatement();
    }


    private function deleteKey(int $id){
        $this->renderer->queryBuilder->setMode(3)
            ->setTable('Codes')
            ->addCond('Codes','id',0,$id,false)
            ->executeStatement();
    }

    public function getFavourites(int $id){
        $this::$dontRender=true;
        $favs=$this->renderer->queryBuilder->setMode(0)
            ->setTable('favourite')
            ->addCond('favourite','productfk',0,$id,false)
            ->executeStatement();
        echo count($favs);
        }
}