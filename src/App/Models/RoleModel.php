<?php
/**
 * This file contains the src/App/Models/RolesModel.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: RoleModel.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
namespace App\Models;

use Framework\Model;
use PDO;

class RoleModel extends Model {

    protected string $table = "tbl_roles";

    /**
     * Updates a record in the database table.
     *
     * @param array $data The array containing the data to update.
     *                    Example: ['colName' => 'New Value', 'colId' => 1]
     * @return array The updated record as an associative array.
     *               Example: ['colName' => 'New Value']
     */
    public function updateRecord(array $data): bool|array {
        $conn = $this->database->getConnection();
        $sql = "UPDATE $this->table SET colName = :colName WHERE colId = :colId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(param: ":colName", value: $data["colName"]);
        $stmt->bindValue(param: ":colId", value: $data["colId"], type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }
}