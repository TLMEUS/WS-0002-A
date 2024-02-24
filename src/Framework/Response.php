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

class Response {
    private string $body = "";

    private array $headers = [];

    private int $status_code = 0;

    /**
     * Set the status code.
     *
     * @param int $code The status code to set.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function setStatusCode(int $code): void {
        $this->status_code = $code;
    }

    /**
     * Redirect the user to the specified URL.
     *
     * @param string $url The URL to redirect to.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function redirect(string $url): void {
        $this->addHeader("Location: $url");
    }

    /**
     * Adds a header to the headers array.
     *
     * @param string $header The header to be added.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function addHeader(string $header): void {
        $this->headers[] = $header;
    }

    /**
     * Sets the body of the request.
     *
     * @param string $body The body content to be set.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function setBody(string $body): void {
        $this->body = $body;
    }

    /**
     * Retrieves the body of the current object.
     *
     * @return string The body of the current object.
     *
     * @noinspection PhpUnused
     */
    public function getBody(): string {
        return $this->body;
    }

    /**
     * Sends the HTTP response.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function send(): void {
        if ($this->status_code) {
            http_response_code($this->status_code);
        }
        foreach ($this->headers as $header) {
            header($header);
        }
        echo $this->body;
    }
}