<?php
/**
 * This file contains the src/App/Models/UserModel.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: UserModel.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Copyright: 01/2024
 */
namespace App\Models;

use Framework\Model;
use Framework\Response;
use PDO;

/**
 * Class UserModel
 *
 * Represents a user model that extends the base.tmp model class.
 *
 * @package     App\Models
 */
class UserModel extends Model {
    protected string $table = "tbl_users";

    /**
     * Retrieves all records from the table.
     *
     * @return array An array containing all the records.
     */
    public function findAll(): array {
        $pdo = $this->database->getConnection();
        $sql = "SELECT colId, colName as colUName, (SELECT colName FROM tbl_departments WHERE 
            colId = Users.colDepartment) AS colDname, (SELECT coLName FROM tbl_roles WHERE 
            colId = Users.colRole) AS colRname FROM $this->table AS Users ORDER BY Users.colId";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(mode: PDO::FETCH_ASSOC);
    }

    public function find(string $id): array {
        $pdo = $this->database->getConnection();
        $sql = "SELECT colId, colName as colUName, (SELECT colName FROM tbl_departments WHERE 
            colId = Users.colDepartment) AS colDname, (SELECT coLName FROM tbl_roles WHERE 
            colId = Users.colRole) AS colRname FROM $this->table AS Users WHERE Users.colId = :colId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(param: ":colId", value: intval($id), type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }

    /**
     * Inserts a record into the database.
     *
     * @param array $data The data to be inserted. The array should follow the format:
     *                    [
     *                        'columnName1' => 'value1',
     *                        'columnName2' => 'value2',
     *                        ...
     *                    ]
     *
     * @return bool Returns true if the record was successfully inserted, false otherwise.
     */
    public function insert(array $data): bool {
        $password = password_hash($data['colPassword'], algo: PASSWORD_DEFAULT);
        $data['colPassword'] = $password;
        return parent::insert($data);

    }
}