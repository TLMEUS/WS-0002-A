<?php
/**
 * This file contains the src/App/database.php class for project WS-0002-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * File Name: database.php
 * File Author: Dave Hollingworth
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace App;

use PDO;

/**
 * Database Class
 *
 * This class represents a database connection.
 * It provides methods to establish and retrieve the database connection.
 */
class Database {
    private ?PDO $pdo = null;

    /**
     * Class Constructor.
     *
     * @param string $host The hostname or IP address of the database server.
     * @param string $name The name of the database.
     * @param string $user The username for the database connection.
     * @param string $password The password for the database connection.
     *
     * @return void
     */
    public function __construct(private readonly string $host,
                                private readonly string $name,
                                private readonly string $user,
                                private readonly string $password) {
    }

    /**
     * Gets the database connection.
     *
     * @return PDO The PDO object representing the database connection.
     */
    public function getConnection(): PDO {
        if ($this->pdo === null) {
            $dsn = "mysql:host=$this->host;dbname=$this->name;charset=utf8;port=3306";
            $this->pdo = new PDO($dsn, $this->user, $this->password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return $this->pdo;
    }
}