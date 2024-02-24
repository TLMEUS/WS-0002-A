<?php
/**
 * This file contains the src/Framework/Controller.php class for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: Controller.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

/**
 * Class Controller
 *
 * This abstract class serves as the base.tmp class for all controllers in the system.
 * It provides common functionality for managing request and response objects, as well as rendering views and performing redirects.
 */
abstract class Controller{
    protected Request $request;
    protected Response $response;
    protected TemplateViewerInterface $viewer;

    /**
     * Set the response.
     *
     * @param Response $response The response object.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function setResponse(Response $response): void {
        $this->response = $response;
    }

    /**
     * Sets the request object.
     *
     * @param Request $request The request object to set.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function setRequest(Request $request): void {
        $this->request = $request;
    }

    /**
     * Sets the viewer object.
     *
     * @param TemplateViewerInterface $viewer The viewer object to set.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function setViewer(TemplateViewerInterface $viewer): void {
        $this->viewer = $viewer;
    }

    /**
     * Sets the view template and data, rendering the template and returning the response.
     *
     * @param string $template The path to the view template.
     * @param array $data An optional array of data to pass to the view template (default: []).
     *
     * @return Response The response object containing the rendered view.
     *
     * @noinspection PhpUnused
     */
    protected function view(string $template, array $data = []): Response {
        $this->response->setBody($this->viewer->render($template, $data));
        return $this->response;
    }

    /**
     * Redirects to the specified URL.
     *
     * @param string $url The URL to redirect to.
     *
     * @return Response The response object.
     *
     * @noinspection PhpUnused
     */
    protected function redirect(string $url): Response {
        $this->response->redirect($url);
        return $this->response;
    }
}