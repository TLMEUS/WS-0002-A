<?php
/**
 * This file contains the src/Framework/Model.php interface for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: Model.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

use PDO;
use App\Database;
use PDOException;

/**
 * Class Model
 *
 * The base.tmp model class for database interaction.
 */
abstract class Model {
    protected string $table;

    protected array $errors = [];

    /**
     * Updates a record in the database.
     *
     * @param string $id The ID of the record to update.
     * @param array $data The data to update the record with.
     *
     * @return bool Returns true if the update was successful, false otherwise.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id, array $data): bool {
        $this->validate($data);
        if ( ! empty($this->errors)) {
            return false;
        }
        $sql = "UPDATE {$this->getTable()} ";
        unset($data["id"]);
        $assignments = array_keys($data);
        array_walk(array: $assignments, callback: function (&$value) {$value = "$value = ?";});
        $sql .= " SET " . implode(separator: ", ", array: $assignments);
        $sql .= " WHERE colId = ?";
        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql);
        $i = 1;
        foreach ($data as $value) {
            $type = match(gettype($value)) {
                "boolean" => PDO::PARAM_BOOL,
                "integer" => PDO::PARAM_INT,
                "NULL" => PDO::PARAM_NULL,
                default => PDO::PARAM_STR
            };
            $stmt->bindValue($i++, $value, $type);
        }
        $stmt->bindValue($i, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Validates the given data.
     *
     * @param array $data The data to be validated.
     *
     * @return void
     */
    protected function validate(array $data): void {
    }

    /**
     * Gets the last inserted ID from the database connection.
     *
     * @return string The last inserted ID.
     *
     * @noinspection PhpUnused
     */
    public function getInsertID(): string {
        $conn = $this->database->getConnection();
        return $conn->lastInsertId();
    }

    /**
     * Adds an error message for the specified field.
     *
     * @param string $field The name of the field for which to add the error message.
     * @param string $message The error message to be added.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function addError(string $field, string $message): void {
        $this->errors[$field] = $message;
    }

    /**
     * Returns an array containing all error messages.
     *
     * @return array The array of error messages.
     *
     * @noinspection PhpUnused
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Retrieves the name of the database table associated with the current class.
     *
     * If the table name is already set and cached, it will return the cached value.
     * Otherwise, it will generate the table name based on the fully qualified class name.
     *
     * @return string The name of the database table associated with the current class.
     *
     * @noinspection PhpUnused
     */
    private function getTable(): string {
        if ($this->table !== null) {
            return $this->table;
        }
        $parts = explode(separator: "\\", string: $this::class);
        return strtolower(string: array_pop(array: $parts));
    }

    /**
     * Constructor method for initializing the object with the specified database instance.
     *
     * @param Database $database The database instance to be used by the object.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function __construct(protected Database $database)    {
    }

    /**
     * Retrieves all records from the table.
     *
     * @return array An array containing all records fetched from the database table.
     *
     * @noinspection PhpUnused
     */
    public function findAll(): array {
        $pdo = $this->database->getConnection();
        $sql = "SELECT * FROM {$this->getTable()}";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Finds a record in the database by its ID.
     *
     * @param string $id The ID of the record to find.
     *
     * @return array|bool Returns an associative array of the record if found, or false if not found.
     *
     * @noinspection PhpUnused
     */
    public function find(string $id): array|bool {
        $conn = $this->database->getConnection();
        $sql = "SELECT * FROM {$this->getTable()} WHERE colId = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(param: ":id", value: $id, type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }


    /**
     * Retrieves a row from the database table based on the specified key-value pair.
     *
     * @param string $key The name of the column to search for.
     * @param string $value The value to search for in the specified column.
     *
     * @return array|bool An associative array representing the retrieved row if found; otherwise, false.
     *
     * @throws PDOException If an error occurs during the database query execution.
     *
     * @noinspection PhpUnused
     */
    public function findByKey(string $key, string $value): array|bool {
        $conn = $this->database->getConnection();
        $sql = "SELECT * FROM $this->table WHERE $key = :value";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":value", $value);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserts data into the database table.
     *
     * @param array $data The data to be inserted into the table.
     *
     * @return bool True if the insertion is successful, false otherwise.
     *
     * @noinspection PhpUnused
     */
    public function insert(array $data): bool {
        $this->validate($data);
        if ( ! empty($this->errors)) {
            return false;
        }
        $columns = implode(separator: ", ", array: array_keys($data));
        $placeholders = implode(separator: ", ", array: array_fill(start_index: 0, count: count($data), value: "?"));
        $sql = "INSERT INTO {$this->getTable()} ($columns) VALUES ($placeholders)";
        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql);
        $i = 1;
        foreach ($data as $value) {
            $type = match(gettype($value)) {
                "boolean" => PDO::PARAM_BOOL,
                "integer" => PDO::PARAM_INT,
                "NULL" => PDO::PARAM_NULL,
                default => PDO::PARAM_STR
            };
            $stmt->bindValue($i++, $value, $type);
        }
        return $stmt->execute();
    }

    /**
     * Deletes a record from the table based on the specified ID.
     *
     * @param string $id The ID of the record to be deleted.
     *
     * @return bool Returns true if the delete operation is successful, false otherwise.
     *
     * @throws PDOException If an error occurs while executing the delete statement.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): bool {
        $sql = "DELETE FROM {$this->getTable()} WHERE colId = :id";
        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(param: ":id", value: $id, type: PDO::PARAM_INT);
        return $stmt->execute();
    }
}