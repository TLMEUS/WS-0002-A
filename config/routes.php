<?php
/**
 * This file contains the config/routes.php for project WS-0002-A.
 *
 * File information:
 * Project Name: WS-0002-A
 * Module Name: config
 * File Name: routes.php
 * File Author: Troy L. Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

$router = new Framework\Router;

$router->add(path: "/", params: ["controller" => "home", "action" => "noaccess", "method" => "get"]);
$router->add(path: "/{user:\d+}", params: ["controller" => "home", "action" => "index"]);
$router->add(path: "/denyaccess", params: ["controller" => "home", "action" => "denyaccess"]);
$router->add(path: "/{controller}/update/{id:\d+}", params: ["action" => "update"]);
$router->add(path: "/{controller}/delete/{id:\d+}", params:  ["action" => "delete"]);
$router->add(path: "/{controller}/{action}", params: ["middleware" => "access"]);

return $router;