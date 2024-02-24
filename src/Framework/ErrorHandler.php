<?php
/**
 * This file contains the src/Framework/EventHandler.php class for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: EventHandler.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

use ErrorException;
use Throwable;
use Framework\Exceptions\PageNotFoundException;

/**
 * Class ErrorHandler
 */
class ErrorHandler {

    /**
     * Handles a PHP error by throwing an ErrorException.
     *
     * @param int $errno The level of the error raised.
     * @param string $errstr The error message.
     * @param string $errfile The filename that the error was raised in.
     * @param int $errline The line number that the error was raised at.
     * @return bool This method always throws an ErrorException, so this return type declaration is only for consistency.
     *
     * @throws ErrorException When the error occurs, an ErrorException with the specified error message, error code, file, and line number is thrown.
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Handles an exception.
     *
     * @param Throwable $exception The exception to be handled.
     *
     * @return void
     *
     * @throws Throwable
     */
    public static function handleException(Throwable $exception): void {
        if ($exception instanceof PageNotFoundException) {
            http_response_code(response_code: 404);
            $template = "404.php";
        } else {
            http_response_code(response_code: 500);
            $template = "500.php";
        }
        if ($_ENV["SHOW_ERRORS"]) {
            ini_set(option: "display_errors", value: "1");
        } else {
            ini_set(option: "display_errors", value: "0");
            ini_set(option: "log_errors", value: "1");
            //require "views/$template";
            require dirname(path: __DIR__, levels: 2) . "/views/$template";
        }
        throw $exception;
    }    
}