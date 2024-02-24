<?php
/**
 * This file contains the src/App/Models/RoleAccessModel.php file for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: RoleAccessModel.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
namespace App\Models;

use Framework\Model;
use PDO;

/**
 * Class RoleAccessModel
 *
 * This class represents the role access model in the application.
 *
 * @package App\Models
 *
 * @noinspection PhpUnused
 */
class RoleAccessModel extends Model {

    protected string $table = "tbl_role_access";

    public function findAll(): array {
        $pdo = $this->database->getConnection();
        $sql = "SELECT colId, (SELECT colName FROM tbl_departments WHERE colId = RoleAccess.colDepartment) AS colDname, 
                (SELECT coLName FROM tbl_roles WHERE colId = RoleAccess.colRole) AS colRname,
                (SELECT colName FROM tbl_applications WHERE colId = RoleAccess.colApplication) AS colAname,
                colAccess
                FROM $this->table AS RoleAccess ORDER BY RoleAccess.colId";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(mode: PDO::FETCH_ASSOC);
    }

    /**
     * Updates a record in the database.
     *
     * @param array $data The data to update the record with.
     *                    The array should include the following keys:
     *                    - colId: The ID of the record to update.
     *                    - colDepartment: The value to set for the colDepartment column.
     *                    - colRole: The value to set for the colRole column.
     *                    - colApplication: The value to set for the colApplication column.
     *
     * @return bool|array Returns either a boolean value indicating if the update was successful,
     *   or an associative array representing the updated record on success.
     */
    public function updateRecord(array $data): bool|array {
        $conn = $this->database->getConnection();
        $sql = "UPDATE $this->table SET colAccess = :colAccess WHERE colId = :colId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(param: ":colId", value: $data["colId"], type: PDO::PARAM_INT);
        $stmt->bindValue(param: ":colAccess", value: $data['colAccess'], type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }
}