<?php
/**
 * This file contains the src/Framework/RequestHandlerInterface.php interface for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: RequestHandlerInterface.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
namespace Framework;

/**
 * Interface RequestHandlerInterface
 *
 * This interface represents a request handler which is responsible for handling HTTP requests and returning a response.
 */
interface RequestHandlerInterface {

    /**
     * Handles the given request and returns a response.
     *
     * @param Request $request The request to handle.
     *
     * @return Response The generated response.
     */
    public function handle(Request $request): Response;
}