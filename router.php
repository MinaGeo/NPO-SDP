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
        echo "Entering $url";
        foreach ($this->configs->ROUTES as $route => $handler) {
            $pattern = $this->buildPattern($route);
            // Debugging: Uncomment the line below to see patterns and URLs
            echo "<pre>Pattern: $pattern | URL: $url</pre>";

            if (preg_match($pattern, $url, $matches)) {
                // Debugging: Uncomment the line below to confirm route match
                // echo "entered </br>";

                array_shift($matches); // Remove the full match
                $this->invokeControllerAction($handler, $matches);
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
