<?php
/**
 * This file contains the src/Framework/Request.php class for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: Request.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

/**
 * Represents an HTTP request.
 *
 * The Request class encapsulates the information related to an HTTP request,
 * including the URI, request method, query parameters, post data, uploaded files,
 * cookies, and server variables.
 */
class Request
{
    /**
     * Class Constructor
     *
     * @param string $uri The request URI.
     * @param string $method The request method (e.g. GET, POST).
     * @param array $get The GET parameters.
     * @param array $post The POST parameters.
     * @param array $files The uploaded files.
     * @param array $cookie The cookie data.
     * @param array $server The server data.
     * @return void
     */
    public function __construct(public string $uri,
                                public string $method,
                                public array  $get,
                                public array  $post,
                                public array  $files,
                                public array  $cookie,
                                public array  $server) {
    }

    /**
     * Creates an instance of the class from the global variables.
     *
     * @return static Returns a new instance of the class populated with values from the global variables.
     */
    public static function createFromGlobals(): static
    {
        return new static(
            $_SERVER["REQUEST_URI"],
            $_SERVER["REQUEST_METHOD"],
            $_GET,
            $_POST,
            $_FILES,
            $_COOKIE,
            $_SERVER
        );
    }
}