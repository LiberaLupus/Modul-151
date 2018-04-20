<?php

namespace services;


class SessionManager
{

    public function startSession(){
        if (session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }

    }

    public function setSessionArray(string $key, array $values){
        if ($values) {
            $_SESSION[$key] = serialize($values);
        }
    }

    public function getSessionArray(string $value){
        if (isset($_SESSION[$value])){
            return unserialize($_SESSION[$value]);
        }
    }

    public function setSessionItem(string $array, $key, $value){
        if (isset($_SESSION[$array]) && $value){
            $session = unserialize($_SESSION[$array]);
            $session[$key] = $value;
            $_SESSION[$array] = serialize($session);
        }
    }


    public function addSessionItem(string $array, $key, $key2, $value){
        if (isset($_SESSION[$array]) && $value){
            $session = unserialize($_SESSION[$array]);
            $session[$key][$key2] = $value;
            $_SESSION[$array] = serialize($session);
        }
    }


    public function getSessionItem(string $array, $key){
        if (isset($_SESSION[$array]) && unserialize($_SESSION[$array])[$key]){
            return unserialize($_SESSION[$array])[$key];
        }
        return"";
    }


    public function isSet($array){
        if (isset($_SESSION[$array])){
            return true;
        }
    }


    public function unsetSessionArray($array){
        if (isset($_SESSION[$array])){
            unset($_SESSION[$array]);
        }
    }


    public function setCookie($name, $value, $duration = 3600){
        $dur = time() + $duration;
        setcookie($name,$value,$dur);
    }


    public function getCookie($name){
        if (isset($_COOKIE[$name])) {
            return$_COOKIE[$name];
        }
    }

}