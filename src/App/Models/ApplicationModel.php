<?php
/**
 * This file contains the src/App/Models/ApplicationModel.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: ApplicationModel.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Copyright: 01/2024
 */
namespace App\Models {

    use Framework\Model;
    use PDO;

    /**
     * Class ApplicationModel
     *
     * This class represents the application model in the application.
     *
     * @package App\Models
     *
     * @noinspection PhpUnused
     */
    class ApplicationModel extends Model {

        protected string $table = "tbl_applications";

        /**
         * Updates a record in the database with the provided data.
         *
         * @param array $data The data used to update the record.
         *                    The array should include the following keys:
         *                    - 'colName': The new value for the 'colName' column.
         *                    - 'colId': The identifier of the record to update.
         *
         * @return bool|array Returns either a boolean indicating the success of the update operation,
         *                   or an array containing the updated record, fetched using the specified mode.
         *                   If the update fails, false is returned.
         */
        public function updateRecord(array $data): bool|array {
            $conn = $this->database->getConnection();
            $sql = "UPDATE $this->table SET colName = :colName, colDomain = :colDomain WHERE colId = :colId";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(param: ":colId", value: $data["colId"], type: PDO::PARAM_INT);
            $stmt->bindValue(param: ":colName", value: $data["colName"]);
            $stmt->bindVAlue(param: ":colDomain", value: $data["colDomain"]);
            $stmt->execute();
            return $stmt->fetch(mode: PDO::FETCH_ASSOC);
        }
    }
}