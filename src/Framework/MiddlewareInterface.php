<?php
/**
 * This file contains the Framework/MiddlewareInterface.php interface for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: MiddlewareInterface.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
namespace Framework;

/**
 * Represents a middleware interface.
 */
interface MiddlewareInterface {
    /**
     * Processes the given request and passes it to the next request handler.
     *
     * @param Request $request The request to be processed.
     * @param RequestHandlerInterface $next The next request handler in the pipeline.
     *
     * @return Response The response returned by the next request handler.
     */
    public function process(Request $request, RequestHandlerInterface $next): Response;
}