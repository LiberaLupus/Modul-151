<?php


namespace helper;


class fileUploader
{
    public function upload($file, int $type)
    {
        //type 0 == image , type 1 == video
        if($type==0)
        $target_dir = __DIR__."/../assets/images/products/";
        elseif($type==1)
            $target_dir = __DIR__."/../assets/movies/";
        else
            $target_dir = __DIR__."/../assets/audio/";
        $target_file = $target_dir . basename($file["name"]);
        $extension= substr($file['name'], strripos($file['name'],'.'));
        $uploadOk = 1;
        $pictureExtensions= ['jpg','png','jpeg','gif'];
        $videoExtensions=['mp4','ogg','webm'];
        $audioExtensions=['mp3'];
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        if(isset($_POST["submit"])) {
            $check = getimagesize($file["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        if (file_exists($target_file)) {
            $file["name"]=uniqid().$extension;
            $target_file = $target_dir .  basename($file["name"]);
        }
        if ($file["size"] > 1920*1080*6 && $type==0) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        if(($type==0 && array_search(strtolower($fileType),$pictureExtensions)<0) ||
            ($type==1 && !array_search(strtolower($fileType),$videoExtensions)<0) ||
            ($type==2 && !array_search(strtolower($fileType),$audioExtensions)<0)) {
            if($type==0)
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            elseif($type==1)
                echo "Sorry, only MP4, OGG & WEBM files are allowed.";
            else
                echo"Sorry, only MP3 files are allowed.";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Sorry, your file '".$file["name"]."'was not uploaded. Type:";
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                echo "The file ". basename( $file["name"]). " has been uploaded.";
                return$file["name"];
            } else {
                echo "Sorry, there was an error uploading your file.";
                return"";
            }
        }

    }
}