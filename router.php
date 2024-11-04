<?php
$configs = require "server-configs.php";
require_once '../Controllers/_test_FilterController.php';
require_once '../Controllers/_test_SortController.php';
require_once '../Controllers/EventController.php';
require_once '../Controllers/LoginController.php';

class Router
{
    public function route($url)
    {
        global $configs;
        foreach ($configs->ROUTES as $route => $handler) {
            $pattern = $this->buildPattern("/$configs->URL_ROOT/$configs->URL_SUBFOLDER$route");
            // echo "<pre>$pattern\t\t\t\t$url\n</pre>";
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remove the full match
                $this->invokeControllerAction($handler, $matches);
                return;
            }
        }
        echo "404 Not Found";
    }

    private function buildPattern($route)
    {
        return '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route) . '$#';
    }

    private function invokeControllerAction($handler, $params)
    {
        list($controllerName, $action) = explode('@', $handler);
        $controller = new $controllerName();
        $this->callControllerAction($controller, $action, $params);
    }

    private function callControllerAction($controller, $action, $params)
    {
        $reflectionMethod = new ReflectionMethod($controller, $action);
        $methodParameters = $reflectionMethod->getParameters();

        $resolvedParams = [];
        foreach ($methodParameters as $param) {
            $paramName = $param->getName();
            $resolvedParams[] = $params[$paramName] ?? null;
        }

        $reflectionMethod->invokeArgs($controller, $resolvedParams);
    }
}
