<?php
/**
 * This file contains the src/App/Models/UserAccessModel.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: UserAccessModel.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Copyright: 01/2024
 */
namespace App\Models;

use Framework\Model;
use PDO;

/**
 * Class UserAccessModel
 *
 * This class represents a model for user access in an application.
 */
class UserAccessModel extends Model {

    protected string $table = "tbl_user_access";

    /**
     * Validates the user for a given application.
     *
     * @param string $user The username of the user.
     * @param string $app The name of the application.
     *
     * @return bool Returns true if the user is valid for the application, false otherwise.
     */
    public function validateUser(string $user, string $app):bool {
        $sql = "SELECT * FROM $this->table WHERE colUsername = :user AND colApplication = :app";
        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(param: ":user", value: $user);
        $stmt->bindValue(param: ":app", value: $app);
        $stmt->execute();
        $result = $stmt->fetch(mode: PDO::FETCH_ASSOC);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Updates a record in the database based on the provided data.
     *
     * @param array $data The data to update the record with, containing the following keys:
     *                    - colId (int) The identifier of the record to update.
     *                    - colAccess (int) The new access level for the record.
     *
     * @return bool|array False if the update failed, otherwise an associative array with the updated record's data.
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

    public function findAll(): array {
        $pdo = $this->database->getConnection();
        $sql = "SELECT colId, (SELECT colName FROM tbl_users WHERE colId = UserAccess.colName) AS colUname, 
                (SELECT colName FROM tbl_applications WHERE colId = UserAccess.colApplication) AS colAname,
                colAccess
                FROM $this->table AS UserAccess ORDER BY UserAccess.colId";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(mode: PDO::FETCH_ASSOC);
    }
}