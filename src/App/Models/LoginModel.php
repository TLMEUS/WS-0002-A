<?php
/**
 * This file contains the src/App/Models/LoginModel.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: LoginModel.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2024
 */
declare(strict_types=1);

namespace App\Models;

use Framework\Model;
use PDO;

class LoginModel extends Model {

    protected string $table = "tbl_login";

    /**
     * Loads login data for a specific colId.
     *
     * @param string $colId The colId for which to load login data.
     *
     * @return void
     */
    public function loadLogin(string $colId): void {
        $conn = $this->database->getConnection();
        $sql= "SELECT * FROM $this->table WHERE colId = :colId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(param: ":colId", value: $colId, type: PDO::PARAM_INT);
        $stmt->execute();
        $userData = $stmt->fetch(mode: PDO::FETCH_ASSOC);
        foreach ($userData as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

    public function redirectToLogin(): void {
        header(header: "Location: https://login.tlme.us/1");
    }


}