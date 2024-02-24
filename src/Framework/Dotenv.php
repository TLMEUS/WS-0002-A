<?php
/**
 * This file contains the src/Framework/Dotenv.php class for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: Dotenv.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

/**
 * Dotenv class for loading environment variables from a file.
 */
class Dotenv {

    /**
     * Loads environment variables from a file.
     *
     * @param string $path The path of the file to load.
     *
     * @return void
     */
    public function load(string $path): void {
        $lines = file(filename: $path, flags: FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            list($name, $value) = explode(separator: "=", string: $line, limit: 2);
            $_ENV[$name] = $value;
        }
    }
}