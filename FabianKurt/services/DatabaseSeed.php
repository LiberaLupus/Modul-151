<?php

namespace services;
use models\Product;
use models\product_tag;
use models\Tag;
use models\User;

class DatabaseSeed
{
    private $dbConnection;
    private $queryBuilder;



    public function resetDatabase(){
        session_destroy();

        $this->dbConnection = new \PDO('mysql:host=localhost;','root','');
        $this->queryBuilder = new QueryBuilder();

        $this->dbConnection->prepare($this->dropDatabase('RProjekt'))->execute();
        $this->dbConnection->prepare($this->createDatabase('RProjekt'))->execute();
        $this->dbConnection->prepare($this->useDatabase('RProjekt'))->execute();
        //Create Table Statements
        $this->dbConnection->prepare('CREATE TABLE IF NOT EXISTS DBUser (ID INT PRIMARY KEY AUTO_INCREMENT,Email varchar(255),Username varchar(100),Password varchar(255),EndDate datetime);')->execute();
        $this->dbConnection->prepare('CREATE TABLE IF NOT EXISTS Tags (ID INT PRIMARY KEY AUTO_INCREMENT,TagName varchar(100));')->execute();
        $this->dbConnection->prepare('CREATE TABLE IF NOT EXISTS Product_Tag (ID INT PRIMARY KEY AUTO_INCREMENT,TagsFk int,ProductFk int);')->execute();
        $this->dbConnection->prepare('CREATE TABLE IF NOT EXISTS Product (ID INT PRIMARY KEY AUTO_INCREMENT,DBUserFK int,Productname varchar(100),Image varchar(100),Video varchar(100),Views int,Rating int, Description varchar(500) );')->execute();
        $this->dbConnection->prepare('CREATE TABLE IF NOT EXISTS Review(ID INT PRIMARY KEY AUTO_INCREMENT,DBUserFK int,ProductFk int, Title varchar(100), Content varchar(500), Rating int);')->execute();
        $this->dbConnection->prepare('CREATE TABLE IF NOT EXISTS Favourite (ID INT PRIMARY KEY AUTO_INCREMENT,ProductFK int,UserFK int);')->execute();
        $this->dbConnection->prepare('CREATE TABLE IF NOT EXISTS Codes(ID INT PRIMARY KEY AUTO_INCREMENT,Code varchar(32),Valid int);')->execute();
        //Alter Table Statements
        $this->dbConnection->prepare('ALTER TABLE Product_Tag ADD CONSTRAINT FOREIGN KEY (tagFK) REFERENCES Tag(id) ON DELETE RESTRICT;')->execute();
        $this->dbConnection->prepare('ALTER TABLE Product_Tag ADD CONSTRAINT FOREIGN KEY (productFK) REFERENCES Product(id) ON DELETE CASCADE;')->execute();
        $this->dbConnection->prepare('ALTER TABLE Favourite ADD CONSTRAINT FOREIGN KEY (DBUserFK) REFERENCES DBUser(id) ON DELETE CASCADE;')->execute();
        $this->dbConnection->prepare('ALTER TABLE Favourite ADD CONSTRAINT FOREIGN KEY (productFK) REFERENCES Product(id) ON DELETE CASCADE;')->execute();
        //$this->dbConnection->prepare('ALTER TABLE Product ADD CONSTRAINT FOREIGN KEY (Product_Tag) REFERENCES Product_Tag(id) ON DELETE RESTRICT')->execute();
        $this->dbConnection->prepare('ALTER TABLE DBUser ADD CONSTRAINT FOREIGN KEY (cartFK) REFERENCES Cart(id) ON DELETE CASCADE;')->execute();
        $this->dbConnection->prepare('ALTER TABLE Product ADD CONSTRAINT FOREIGN KEY (DBUserFK) REFERENCES DBUser(id) ON DELETE RESTRICT ')->execute();
        $this->dbConnection->prepare('ALTER TABLE Review ADD CONSTRAINT FOREIGN KEY (DBUserFK) REFERENCES DBUser(id) ON DELETE CASCADE;')->execute();


        $this->dbConnection = null;
        $this->dbConnection = DBConnection::getDbConnection();

        //Generate Base Data (Admin/Webhost User, Base Products)
        $user = new User();
        $data = [];
        $data_1 = ['email'=>'root@root.ch','username' =>'root', 'password' => 'root','enddate' => date("Y-m-d H:i:s")];
        $data_2 = ['email'=>'user@user.ch','username' =>'user', 'password' => 'user','enddate' => date("Y-m-d H:i:s")];
        array_push($data,$data_1);
        array_push($data,$data_2);
        foreach($data as $d){
            $_POST['data'] = $data;
            $user->clearEntity();
            $user->patchEntity($d);
            if ($user->isValid()){
                $user->save();
            }
        }


        $product = new Product();
        $prod=[];
        $prod[0] = ['userfk' =>'1','productname' =>'PN1', 'image' =>'PP01.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0.42, 'description' =>'Test description 1'];
        $prod[1] = ['userfk' =>'1','productname' =>'PN2', 'image' =>'PP02.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0, 'description' =>'Test description 2'];
        $prod[2] = ['userfk' =>'1','productname' =>'PN3', 'image' =>'PP03.jpg','video'=>'.mp4','stock' => 42,'price' => 420, 'discount' =>0.42, 'description' =>'Test description 3'];
        $prod[3] = ['userfk' =>'2','productname' =>'PN4', 'image' =>'PP04.jpg','video'=>'.mp4','stock' => 20,'price' => 42, 'discount' =>0, 'description' =>'Test description 4'];
        $prod[4] = ['userfk' =>'1','productname' =>'PN5', 'image' =>'PP05.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0, 'description' =>'Test description 5'];
        $prod[5] = ['userfk' =>'2','productname' =>'PN6', 'image' =>'PP06.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0.42, 'description' =>'Test description 6'];
        $prod[6] = ['userfk' =>'2','productname' =>'PN7', 'image' =>'PP07.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0, 'description' =>'Test description 7'];
        $prod[7] = ['userfk' =>'2','productname' =>'PN8', 'image' =>'PP08.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0, 'description' =>'Test description 8'];
        $prod[8] = ['userfk' =>'1','productname' =>'PN9', 'image' =>'PP09.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0.42, 'description' =>'Test description 9'];
        $prod[9] = ['userfk' =>'2','productname' =>'PN10', 'image' =>'PP10.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0, 'description' =>'Test description 10'];
        $prod[10]= ['userfk' =>'2','productname' =>'PN11', 'image' =>'PP11.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>42, 'description' =>'Test description 11'];
        $prod[11]= ['userfk' =>'2','productname' =>'PN12', 'image' =>'PP12.jpg','video'=>'.mp4','stock' => 42,'price' => 42, 'discount' =>0, 'description' =>'Test description 12'];
        foreach ($prod as $p){
            $_POST['data'] = $prod;
            $product->clearEntity();
            $product->patchEntity($p);
            $product->save();
        }
        $tag = new Tag();
        $tags=[];
        $tags[0]=['tagname'=>'Tag1'];
        $tags[1]=['tagname'=>'Tag2'];
        $tags[2]=['tagname'=>'Tag3'];
        $tags[3]=['tagname'=>'Tag4'];
        $tags[4]=['tagname'=>'Tag5'];
        foreach($tags as $t){
            $_POST['data']=$tags;
            $tag->clearEntity();
            $tag->patchEntity($t);
            $tag->save();
        }

        $this->product_tag($prod,$tags);
    }


    private function dropDatabase($dbName){
        $stmt = "drop database if exists ".$dbName;
        return$stmt;
    }

    private function createDatabase($dbName){
        $stmt = "create database if not exists ".$dbName;
        return$stmt;
    }
    private function useDatabase($dbName){
        $stmt = "use ".$dbName;
        return$stmt;
    }
    private function product_tag($prod, $tags){
        $product_tag=new product_tag();

        $alreadyAdded=[];
        $count=0;
        for($a=0; $a<count($prod); $a++){
            $count++;
            for($i=0; $i<random_int(1,5);$i++){
                $t=random_int(1,count($tags));
                if(!in_array($t,$alreadyAdded)){
                    array_push($alreadyAdded,$t);
                    $tmp=['tagid'=>$t,'productid'=>$count];
                    $_POST['data']=$tmp;
                    $product_tag->clearEntity();
                    $product_tag->patchEntity($tmp);
                    $product_tag->save();
                }
            }
            $alreadyAdded=[];
        }
    }

}