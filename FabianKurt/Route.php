<?php
class Route
{
    public function submit()
    {
        $uri = isset($_REQUEST['uri']) ? $_REQUEST['uri'] : '/';
        $routerFragments = explode("/",$uri);
        $routerFragments[0] = ucfirst($routerFragments[0]) . "Controller";
        $namespace = "\Controller\\" . $routerFragments[0];
        if(class_exists($namespace)){
            $controller = new $namespace;
            $function = $routerFragments[1];
            $controller->viewTemplate = $function . ".php";
            $cookieManager=new \services\CookieManager();
            if($cookieManager->isCookieSet($namespace."\\".$function)){
                $cookieManager->setCookie($namespace."\\".$function,$cookieManager->getCookie($namespace."\\".$function)+1);
            }else{
                $cookieManager->setCookie($namespace."\\".$function,1);
            }
            @call_user_func_array([$controller, $function],array_slice($routerFragments,2));
        }else{
            throw new Error("Page not found", 404);
        }

    }

}










