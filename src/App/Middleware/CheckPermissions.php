<?php
/**
 * This file contains the Middleware/CheckPermissions.php file for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: App
 * Section Name: Middleware
 * File Name: CheckPermissions.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
declare(strict_types=1);

namespace App\Middleware;

use Framework\Request;
use Framework\Response;
use Framework\RequestHandlerInterface;
use Framework\MiddlewareInterface;

/**
 * Class CheckPermissions
 *
 * This class implements the MiddlewareInterface and is responsible for checking the permissions
 * before allowing access to a requested resource.
 *
 * @implements MiddlewareInterface
 */
readonly class CheckPermissions implements MiddlewareInterface {

    /**
     * Class constructor.
     *
     * @param Response $response The Response object used for handling HTTP responses.
     *
     * @return void
     */
    public function __construct(private Response $response){
    }

    /**
     * Process the request.
     *
     * @param Request $request The Request object representing the incoming HTTP request.
     * @param RequestHandlerInterface $next The next RequestHandler in the middleware chain.
     *
     * @return Response The corresponding HTTP response.
     */
    public function process(Request $request, RequestHandlerInterface $next): Response {
        if ($_ENV["colAccess"] != 2) {
            setcookie("LoggedUser",$_COOKIE['LoggedUser'], time() + (60 * 5), "/", "tlme.us");
            return $next->handle($request);
        }
        $this->response->redirect(url: "https://userman.tlme.us/denyaccess");
        return $this->response;
    }
}