<?php
/**
 * This file contains the src/Framework/MiddlewareRequestHandler.php interface for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: MiddlewareRequestHandler.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

/**
 * Class MiddlewareRequestHandler
 *
 * An implementation of the RequestHandlerInterface that allows the execution of middlewares
 * before passing the request to a controller request handler.
 */
class MiddlewareRequestHandler implements RequestHandlerInterface {

    /**
     * Constructor for the class.
     *
     * @param array $middlewares The array of middlewares to be used.
     * @param ControllerRequestHandler $controller_handler The instance of ControllerRequestHandler class.

     * @return void
     */
    public function __construct(private array $middlewares,
                                private ControllerRequestHandler $controller_handler) {
    }

    /**
     * Handles a request by processing the middlewares and eventually
     * passing the request to the controller handler.
     *
     * @param Request $request The request to be handled.
     *
     * @return Response|null The response generated after handling the request.
     */
    public function handle(Request $request): Response {
        $middleware = array_shift(array: $this->middlewares);
        if ($middleware === null) return $this->controller_handler->handle($request);
        return $middleware->process($request, $this);
    }
}