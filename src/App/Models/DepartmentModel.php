<?php
/**
 * This file contains the src/AppModels/DepartmentModel.php file for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: DepartmentModel.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
namespace App\Models;

use Framework\Model;
use PDO;

/**
 * Class DepartmentModel
 *
 * Represents a department model that extends the base.tmp model class.
 *
 * @package     App\Models
 */
class DepartmentModel extends Model {

    protected string $table = "tbl_departments";

    /**
     * Updates a record in the database table.
     *
     * @param array $data An associative array containing the data to be updated.
     *                    The array should have the following structure:
     *                    [
     *                        'colName' => 'updated value',
     *                        'colId' => 'record identifier'
     *                    ]
     * @return array|bool The updated record as an associative array, or null if no record was updated.
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