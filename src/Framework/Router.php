<?php
/**
 * This file contains the src/Framework/Response.php interface for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: Response.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

/**
 * Class Router
 *
 * The Router class is responsible for managing routes and matching incoming paths
 * to the appropriate route.
 */
class Router {
    private array $routes = [];

    /**
     * Adds a new route to the routes array.
     *
     * @param string $path The path of the route.
     * @param array $params Optional. The parameters of the route. Defaults to an empty array.
     *
     * @return void
     */
    public function add(string $path, array $params = []): void {
        $this->routes[] = ["path" => $path, "params" => $params];
    }

    /**
     * Match a given path and HTTP method with defined routes.
     *
     * @param string $path The path to match against the routes.
     * @param string $method The HTTP method to match against the routes.
     *
     * @return array|bool Returns an array of route parameters if there is a match, otherwise false.
     */
    public function match(string $path, string $method): array|bool {
        $path = urldecode(string: $path);
        $path = trim(string: $path, characters: "/");
        foreach ($this->routes as $route) {
            $pattern = $this->getPatternFromRoutePath($route["path"]);
            if (preg_match(pattern: $pattern, subject: $path, matches: $matches)) {
                $matches = array_filter(array: $matches, callback: "is_string", mode: ARRAY_FILTER_USE_KEY);
                $params = array_merge($matches, $route["params"]);
                if (array_key_exists(key: "method", array: $params)) {
                    if (strtolower($method) !== strtolower($params["method"])) {
                        continue;
                    }
                }
                return $params;
            }
        }
        return false;
    }

    /**
     * Generate a regular expression pattern from a given route path.
     *
     * @param string $route_path The route path to generate the pattern from.
     *
     * @return string Returns the generated regular expression pattern.
     */
    private function getPatternFromRoutePath(string $route_path): string {
        $route_path = trim($route_path, characters: "/");
        $segments = explode(separator: "/", string: $route_path);
        $segments = array_map(function(string $segment): string {
            if (preg_match(pattern: "#^\{([a-z][a-z0-9]*)}$#", subject: $segment, matches: $matches)) {
                return "(?<" . $matches[1] . ">[^/]*)";
            }
            if (preg_match(pattern: "#^\{([a-z][a-z0-9]*):(.+)}$#", subject: $segment, matches: $matches)) {
                return "(?<" . $matches[1] . ">" . $matches[2] . ")";
            }
            return $segment;
        }, $segments);
        return "#^" . implode(separator: "/", array: $segments) . "$#iu";
    }
}