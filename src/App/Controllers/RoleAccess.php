<?php
/**
 * This file contains the src/App/Controllers/RoleAccess.php file for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Controllers
 * File Name: RoleAccess.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
namespace App\Controllers;

use App\Database;
use App\Models\ApplicationModel;
use App\Models\DepartmentModel;
use App\Models\RoleAccessModel;
use App\Models\RoleModel;
use App\Models\ValidateModel;
use Framework\Controller;
use Framework\Response;

/**
 * RoleAccessModel Class
 *
 * This class represents the RoleAccessModel controller.
 * It is responsible for handling all the role access permissions to the application in the system.
 *
 * @package     App
 * @subpackage  Controllers
 *
 * @noinspection PhpUnused
 */
class RoleAccess extends Controller {

    private DepartmentModel $departmentModel;
    private RoleModel $roleModel;
    private ApplicationModel $applicationModel;
    private RoleAccessModel $roleAccessModel;
    protected ValidateModel $validateModel;

    /**
     * Constructs a new instance of the class.
     *
     * This method initializes a new instance of the class by creating a new `Database` object with the provided
     * database host, name, user, and password. It then creates a new `RoleAccessModel` object using the created
     * `Database` object and assigns it to the `roleAccessModel` property.
     *
     * Additionally, it creates a new `ValidateModel` object using the `roleAccessModel` as a dependency and assigns
     * it to the `validateModel` property.
     *
     * @noinspection PhpUnused
     */
    public function __construct() {
        $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->departmentModel = new DepartmentModel($database);
        $this->roleModel = new RoleModel($database);
        $this->applicationModel = new ApplicationModel($database);
        $this->roleAccessModel = new RoleAccessModel($database);
        $this->validateModel = new ValidateModel($this->roleAccessModel);
    }

    /**
     * Retrieves a list of role accesses and renders the index view.
     *
     * @return Response The response object.
     *
     * @noinspection PhpUnused
     */
    public function index(): Response {
        $accesslist = $this->roleAccessModel->findAll();
        return $this->view(template: 'RoleAccess/index.tlmt', data: [
            'accesslist' => $accesslist
        ]);
    }

    /**
     * Create method.
     *
     * Render the view for creating a RoleAccessModel.
     *
     * @return Response
     *
     * @noinspection PhpUnused
     */
    public function create(): Response {
        $departments = $this->departmentModel->findAll();
        $roles = $this->roleModel->findAll();
        $applications = $this->applicationModel->findAll();
        //var_dump($applications);
        return $this->view(template: 'RoleAccess/create.tlmt', data: [
            'departments' => $departments,
            'roles' => $roles,
            'applications' => $applications
        ]);
    }


    /**
     * Creates a role access record.
     *
     * This method creates a role access record by performing the following steps:
     *
     * 1. Calls the `validatePost` method of the `ValidateModel` to check if the POST data is valid.
     * 2. If the POST data is not valid, it returns an error view with the title, message, and error code set
     *    to "Method Not Allowed" and "405" respectively.
     * 3. If the POST data is valid, it calls the `validateData` method of the `ValidateModel` to validate the POST data.
     * 4. If the POST data is valid, it calls the `insert` method of the `RoleAccessModel` to insert the data into the database.
     *    If the insertion fails, it returns an error view with the title, message, and error code set to "Role Access Record Entry Error"
     *    and "500" respectively.
     * 5. If the POST data is not valid, it returns an error view with the title, message, and error code set to "Role Access Record Entry Error"
     *    and "406" respectively.
     * 6. If all the previous steps are successful, it calls the `index` method to display the index view.
     *
     * @return Response The response object for displaying the index view.
     *
     * @noinspection PhpUnused
     */
    public function createRoleAccess(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"]);
        }
        if ($this->validateModel->validateData($_POST, op: 'RAR')) {
            if (!$this->roleAccessModel->insert($_POST))
                $this->view(template: "error.tlmt", data: [
                    "title" => "Role Access Record Entry Error",
                    "message" => "Unable to save record",
                    "errorcode" => "500"
                ]);
        } else {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Role Access Record Entry Error",
                "message" => "Invalid Information Entered",
                "errorcode" => "406"
            ]);
        }
        return $this->index();
    }


    /**
     * Updates a role access record.
     *
     * This method retrieves the role access record with the provided ID from the `roleAccessModel`. If the record
     * is found, it returns a view with the update form pre-populated with the column ID and column name of the
     * retrieved record.
     *
     * If the record is not found, it returns an error view with a 404 error code indicating that the record could
     * not be found.
     *
     * @param string $id The ID of the role access record to update.
     *
     * @return Response The response object containing the view with the update form or the error view if the record
     *                 is not found.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id): Response {
        $old = $this->roleAccessModel->find($id);
        if(!$old) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Role Access Record Entry Error",
                "message" => "Record not found",
                "errorcode" => "404"
            ]);
        } else {
            $department = $this->departmentModel->find($old['colDepartment']);
            $role = $this->roleModel->find($old['colRole']);
            $application = $this->applicationModel->find($old['colApplication']);
            return $this->view(template: 'RoleAccess/update.tlmt', data: [
                "colId" => $old['colId'],
                "colDepartment" => $department['colName'],
                "colRole" => $role['colName'],
                "colApplication" => $application['colName'],
                "colAccess" => $old['colAccess']
            ]);
        }
    }


    /**
     * Update role access.
     *
     * This method updates the role access based on the provided data. It performs the following steps:
     * - Validates the request method. If it is not a POST request, it returns an error response.
     * - Validates the data sent in the request. If the data is invalid, it returns an error response.
     * - Updates the role access data using the validated data. If the update is unsuccessful, it returns an error response.
     * - If all steps are successful, it returns the index page.
     *
     * @return Response The response object representing the updated role access.
     *
     * @noinspection PhpUnused
     */
    public function updateRoleAccess(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST, op: "RAR")) {
            if($this->roleAccessModel->updateRecord(data: $_POST)) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Save Error",
                    "message" => "Unable to update record",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }


    /**
     * Delete role access.
     *
     * This method deletes a role access record based on the provided ID. It performs the following steps:
     * - Finds the role access record with the given ID. If the record does not exist, it returns an error response.
     * - If the record is found, it returns a view displaying the details of the role access to be deleted.
     *
     * @param string $id The ID of the role access record to delete.
     *
     * @return Response The response object representing the role access deletion view.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): Response {
        $old = $this->roleAccessModel->find($id);
        if(!$old) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Delete Record Error",
                "message" => "Unable to locate record",
                "errorcode" => "404"
            ]);
        }
        $department = $this->departmentModel->find($old['colDepartment']);
        $role = $this->roleModel->find($old['colRole']);
        $application = $this->applicationModel->find($old['colApplication']);
        return $this->view(template: 'RoleAccess/delete.tlmt', data: [
            "colId" => $old['colId'],
            "colDepartment" => $department['colName'],
            "colRole" => $role['colName'],
            "colApplication" => $application['colName'],
        ]);
    }


    /**
     * Delete application.
     *
     * This method deletes an application record based on the provided data. It performs the following steps:
     * - Validates the request method. If it is not a POST request, it returns an error response.
     * - Validates the data sent in the request for delete operation.
     * - Deletes the application record using the validated data. If the deletion is unsuccessful, it returns an error response.
     * - If all steps are successful, it returns the index page.
     *
     * @return Response The response object representing the index page.
     *
     * @noinspection PhpUnused
     */
    public function deleteRecord(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        $this->validateModel->validateData($_POST, op: "delete");
        if(!$this->roleAccessModel->delete($_POST['colId'])) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Delete Record Error",
                "message" => "Unable to delete record",
                "errorcode" => "500"
            ]);
        }
        return $this->index();
    }
}