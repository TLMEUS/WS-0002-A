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

/**
 * Class ValidateModel
 *
 * This class provides validation methods for data processing operations.
 */
class ValidateModel {

    private Object $testModel;

    /**
     * Class constructor.
     *
     * @param Object $testObject The test object to be assigned to the testModel property.
     *
     * @return void
     */
    public function __construct(Object $testObject) {
        $this->testModel = $testObject;
    }

    /**
     * Validates if the current request is a POST request.
     *
     * @return bool Returns true if the current request method is POST, false otherwise.
     */
    public function validatePost(): bool {
        if($_SERVER["REQUEST_METHOD"] != "POST") {
            return false;
        }
        return true;
    }

    /**
     * Validates the given data based on the specified operation.
     *
     * @param array $data The data to be validated.
     * @param string $op The operation to be performed. Default value is 'create'.
     *
     * @return bool True if the data is valid for the specified operation, false otherwise.
     */
    public function validateData(array $data, string $op = 'create'): bool {
        switch ($op) {
            case "delete":
                $test = $data["colId"] ?? null;
                return $this->validateId($test);
            case "update":
                if ($this->validateField($data["colDepartment"]) && $this->validateField($data["colRole"]))
                    return true;
                break;
            case "UAPP":
            case "UAR":
            case "RAR":
                return true;
            default:
                $test = $data["colName"] ?? null;
                if ($this->validateName($test) && !$this->validateExistence($test))
                    return true;
                break;
        }
        return false;
    }

    /**
     * Validates whether the given ID exists in the testModel.
     *
     * @param string $id The ID to validate.
     *
     * @return bool Returns true if the ID exists in the testModel, false otherwise.
     */
    private function validateId(string $id): bool {
        if ($this->testModel->findByKey("colId", $id)) {
            return true;
        }
        return false;
    }

    /**
     * Validates a given name.
     *
     * @param string|null $test The name to be validated.
     *
     * @return bool Returns true if the name is valid, false otherwise.
     */
    private function validateName(?string $test): bool {
        if (!$test) {
            return false;
        }
        return true;
    }

    /**
     * Validates the existence of a given string in the testModel object.
     *
     * @param string|null $test The string to be validated.
     *
     * @return bool Returns true if the string exists in the testModel object,
     *              otherwise returns false.
     */
    private function validateExistence(?string $test): bool {
        if($this->testModel->findByKey("colName", $test)) {
            return true;
        }
        return false;
    }

    private function validateField(mixed $field): bool {
        if(empty($field)) {
            return false;
        }
        return true;
    }
}