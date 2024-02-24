<?php
/**
 * This file contains the config/middleware.php for project WS-0002-A.
 *
 * File information:
 * Project Name: WS-0002-a
 * Module Name: config
 * File Name: middleware.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

return [
    "access" => \App\Middleware\CheckPermissions::class
];