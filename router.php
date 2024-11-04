<?php
// src/Router.php

class Router
{
    private $configs;

    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    public function route($url)
{
    // Separate the URL path from the query string--------->RAFIKKKKK
    $parsedUrl = parse_url($url);
    $path = $parsedUrl['path'];
    parse_str($parsedUrl['query'] ?? '', $queryParams); // Get query parameters as an associative array

    echo "Entering $path";
    foreach ($this->configs->ROUTES as $route => $handler) {
        $pattern = $this->buildPattern($route);
        
        // Debugging-------------------->RAFIKKKK
        echo "<pre>Pattern: $pattern | Path: $path</pre>";

        if (preg_match($pattern, $path, $matches)) {
            array_shift($matches); // Remove the full match
            $params = array_merge($matches, $queryParams); // Combine path params with query params
            $this->invokeControllerAction($handler, $params);
            return;
        }
    }
    echo "404 Not Found";
}


    private function buildPattern($route)
    {
        // Escape slashes for regex and replace {param} with named regex groups
        $pattern = preg_replace('/\//', '\/', $route);
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^\/]+)', $pattern);
        return '/^' . $pattern . '$/';
    }

    private function invokeControllerAction($handler, $params)
    {
        list($controllerName, $action) = explode('@', $handler);

        if (!class_exists($controllerName)) {
            echo "Error: Controller '$controllerName' not found.";
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            echo "Error: Action '$action' not found in controller '$controllerName'.";
            return;
        }

        $this->callControllerAction($controller, $action, $params);
    }

    private function callControllerAction($controller, $action, $params)
    {
        try {
            $reflectionMethod = new ReflectionMethod($controller, $action);
            $methodParameters = $reflectionMethod->getParameters();

            $resolvedParams = [];
            foreach ($methodParameters as $param) {
                $paramName = $param->getName();
                if (isset($params[$paramName])) {
                    $resolvedParams[] = $params[$paramName];
                } elseif ($param->isDefaultValueAvailable()) {
                    $resolvedParams[] = $param->getDefaultValue();
                } else {
                    // Parameter is missing and no default value
                    echo "Error: Missing parameter '$paramName'.";
                    return;
                }
            }

            $reflectionMethod->invokeArgs($controller, $resolvedParams);
        } catch (ReflectionException $e) {
            echo "Error invoking method: " . $e->getMessage();
        }
    }
}
