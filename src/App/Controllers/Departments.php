<?php
/**
 * This file contains the src/App/Controllers/Departments.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Controllers
 * File Name: Departments.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
namespace App\Controllers;

use App\Database;
use App\Models\DepartmentModel;
use App\Models\ValidateModel;
use Framework\Controller;
use Framework\Response;


/**
 * Class Departments
 *
 * Represents a controller class for managing departments.
 *
 * @noinspection PhpUnused
 */
class Departments extends Controller {

    private DepartmentModel $departmentModel;

    protected ValidateModel $validateModel;

    /**
     * Constructs a new instance of the class.
     *
     * This method initializes a new instance of the class by creating a new `Database` object with the provided
     * database host, name, user, and password. It then creates a new `DepartmentModel` object using the created
     * `Database` object and assigns it to the `departmentModel` property.
     *
     * Additionally, it creates a new `ValidateModel` object using the `departmentModel` as a dependency and assigns
     * it to the `validateModel` property.
     *
     * @noinspection PhpUnused
     */
    public function __construct() {
        $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->departmentModel = new DepartmentModel($database);
        $this->validateModel = new ValidateModel($this->departmentModel);
    }

    /**
     * Retrieves all the departments from the database and returns a Response object
     * containing the rendered view.
     *
     * @return Response The response containing the rendered view.
     */
    public function index(): Response {
        $departments = $this->departmentModel->findAll();
        return $this->view(template: "Departments/index.tlmt", data: [
            'departments' => $departments
        ]);
    }

    /**
     * Creates a new department.
     *
     * This method returns a Response object which contains the view template for creating a new department.
     *
     * @return Response The Response object containing the view template for creating a new department.
     *
     * @noinspection PhpUnused
     */
    public function create(): Response {
        return $this->view(template: "Departments/create.tlmt");
    }

    /**
     * Creates a new department.
     *
     * Verifies if the request is a POST request and returns an error response if it is not.
     * Validates the data received in the POST request and returns an error response if the data is invalid.
     * Calls the `createDep` method to create the department with the validated data.
     * Returns a response with the index page.
     *
     * @return Response The response object containing the index page.
     *
     * @noinspection PhpUnused
     */
    public function createDepartment(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"]);
        }
        if ($this->validateModel->validateData($_POST)) {
            if (!$this->departmentModel->insert($_POST)) {
                $this->view(template: "error.tlmt", data: [
                    "title" => "Department Entry Error",
                    "message" => "Unable to save department",
                    "code" => "500"
                ]);
            }
        } else {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Department Entry Error",
                "message" => "Invalid Information Entered",
                "errorcode" => "406"
            ]);
        }
        return $this->index();
    }

    /**
     * Updates a department record based on the provided ID.
     *
     * @param string $id The ID of the department to update.
     *
     * @return Response Returns a Response object.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id): Response {
        $old_dep = $this->departmentModel->find($id);
        if(!$old_dep) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Department Entry Error",
                "message" => "Record not found",
                "errorcode" => "404"
            ]);
        } else {
            return $this->view(template: 'Departments/update.tlmt', data: [
                "colId" => $old_dep['colId'],
                "colName" => $old_dep['colName']
            ]);
        }
    }

    /**
     * Updates a department based on POST data.
     *
     * This method verifies if the current request is a POST request using the verifyPostRequest method.
     * If the request is not a POST request, the method returns an error view with status code 405 (Method Not Allowed).
     * If the request is a POST request, the method validates the POST data using the validateData method.
     * If the data is valid, the method calls the updateRecord method of the departmentModel to update the department.
     * If the update is successful, the method returns the index view.
     * If the update is not successful or the data is not valid, the method returns an error view with status code 500
     * (Save Error).
     *
     * @return Response Returns a Response object representing the view to be shown to the user.
     *
     * @noinspection PhpUnused
     */
    public function updateDepartment(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST)) {
            if($this->departmentModel->updateRecord($_POST)) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Save Error",
                    "message" => "Unable to update department",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }

    /**
     * Deletes a department.
     *
     * @param string $id The ID of the department to be deleted.
     *
     * @return Response Returns a Response object that represents the view of the delete page.
     *                 If the department with the specified ID doesn't exist, a blank view with
     *                 an error message is returned.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): Response {
        $oldDep = $this->departmentModel->find(id: $id);
        if(!$oldDep) {
            return $this->view(template: "blank.tlmt", data: [
                "title" => "Missing record",
                "message" => "Unable to locate record",
                "errorcode" => "404"
            ]);
        }
        $colId = $oldDep['colId'];
        $colName = $oldDep['colName'];
        return $this->view(template: 'Departments/delete.tlmt', data: [
            "colId" => $colId,
            "colName" => $colName
        ]);
    }

    /**
     * Deletes a department.
     *
     * This method verifies if the current request is a POST request. If not, it returns an error response with a
     * "Method Not Allowed" message and error code 405.
     *
     * It then validates the data sent via the POST request using the `validateData` method with the specified operation
     * "delete". If the data is valid, it attempts to delete the department by calling the `delete` method on the
     * `departmentModel` object with the `colId` provided via the POST request.
     *
     * If the deletion is successful, it returns the result of the `index` method.
     *
     * If any validation or deletion errors occur, it returns an error response with a suitable error message
     * and error code 500.
     *
     * @return Response Returns a response object representing the result of the deletion operation.
     *
     * @noinspection PhpUnused
     */
    public function deleteDepartment(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST, op: "delete")) {
            if(!$this->departmentModel->delete($_POST["colId"])) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Deletion Error",
                    "message" => "Unable to delete department",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }
}