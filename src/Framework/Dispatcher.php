<?php
/**
 * This file contains the src/Framework/ControllerRequestHandler.php class for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: ControllerRequestHandler.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

use ReflectionException;
use ReflectionMethod;
use Framework\Exceptions\PageNotFoundException;
use UnexpectedValueException;

/**
 * Class Dispatcher
 *
 * Responsible for handling HTTP requests and dispatching them to the appropriate controller and action.
 */
readonly class Dispatcher
{
    /**
     * Constructor method for initializing the class.
     *
     * @param Router $router The Router object used for routing.
     * @param Container $container The Container object used for dependency injection.
     * @param array $middleware_classes An array of middleware classes to be used.
     *
     * @return void
     */
    public function __construct(private Router    $router,
                                private Container $container,
                                private array     $middleware_classes) {
    }

    /**
     * Handles a request by processing the route and executing the appropriate controller action.
     *
     * @param Request $request The Request object representing the HTTP request.
     *
     * @return Response The Response object representing the HTTP response.
     *
     * @throws PageNotFoundException If no route matched for the given path and method.
     * @throws ReflectionException
     */
    public function handle(Request $request): Response {
        $path = $this->getPath($request->uri);
        $params = $this->router->match($path, $request->method);
        if ($params === false)
            throw new PageNotFoundException("No route matched for '$path' with method '$request->method'");
        $action = $this->getActionName($params);
        $controller = $this->getControllerName($params);
        $controller_object = $this->container->get($controller);
        $controller_object->setViewer($this->container->get(TemplateViewerInterface::class));
        $controller_object->setResponse($this->container->get(Response::class));
        $args = $this->getActionArguments($controller, $action, $params);
        $controller_handler = new ControllerRequestHandler($controller_object, $action, $args);
        $middleware = $this->getMiddleware($params);
        $middleware_handler = new MiddlewareRequestHandler($middleware, $controller_handler);
        return $middleware_handler->handle($request);
    }

    /**
     * Retrieves an array of middleware classes based on the provided parameters.
     *
     * @param array $params The parameters array containing the "middleware" key.
     * @return array An array of middleware classes.
     * @throws UnexpectedValueException If a middleware class is not found in the config settings.
     * @throws ReflectionException
     */
    private function getMiddleware(array $params): array {
        if ( ! array_key_exists(key: "middleware", array: $params)) {
            return [];
        }
        $middleware = explode(separator: "|", string: $params["middleware"]);
        array_walk(array: $middleware, callback: function(&$value) {
            if ( ! array_key_exists($value, $this->middleware_classes)) {
                throw new UnexpectedValueException(message: "Middleware '$value' not found in config settings");
            }
            $value = $this->container->get($this->middleware_classes[$value]);
        });
        return $middleware;
    }

    /**
     * Method for retrieving action arguments based on controller, action, and parameters.
     *
     * @param string $controller The name of the controller class.
     * @param string $action The name of the action method.
     * @param array $params An associative array of parameters.
     *
     * @return array An associative array of action arguments.
     *
     * @throws ReflectionException
     */
    private function getActionArguments(string $controller, string $action, array $params): array {
        $args = [];
        $method = new ReflectionMethod($controller, $action);
        foreach ($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            $args[$name] = $params[$name];
        }
        return $args;
    }

    /**
     * Get the fully qualified name of the controller based on the given parameters.
     *
     * @param array $params An array of parameters containing "controller" and optionally "namespace".
     *                      The "controller" parameter specifies the controller name.
     *                      The "namespace" parameter specifies the namespace of the controller (optional).
     *
     * @return string The fully qualified name of the controller.
     */
    private function getControllerName(array $params): string {
        $controller = $params["controller"];
        $controller = str_replace(search: "-", replace: "", subject: ucwords(strtolower($controller), "-"));
        $namespace = "App\Controllers";
        if (array_key_exists(key: "namespace", array: $params)) {
            $namespace .= "\\" . $params["namespace"];
        }
        return $namespace . "\\" . $controller;
    }

    /**
     * Gets the action name from the given parameters.
     *
     * @param array $params An array of parameters containing the action name.
     *
     * @return string The formatted action name.
     */
    private function getActionName(array $params): string {
        $action = $params["action"];
        return lcfirst(str_replace(search: "-", replace: "", subject: ucwords(strtolower($action), "-")));
    }

    /**
     * Returns the path from a given URI.
     *
     * @param string $uri The URI from which to retrieve the path.
     *
     * @return string The path extracted from the URI.
     *
     * @throws UnexpectedValueException If the URI is not well-formed.
     */
    private function getPath(string $uri): string {
        $path = parse_url($uri, component: PHP_URL_PATH);
        if ($path === false) {
            throw new UnexpectedValueException(message: "Malformed URL: '$uri'");
        }
        return $path;
    }
}