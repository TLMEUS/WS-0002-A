<?php
/**
 * This file contains the src/App/Controllers/Roles.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Controllers
 * File Name: Roles.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
namespace App\Controllers;

use App\Database;
use App\Models\RoleModel;
use App\Models\ValidateModel;
use Framework\Controller;
use Framework\Response;

/**
 * Class Roles
 *
 * The Roles class represents a controller for managing roles in an application.
 *
 * @package App\Controllers
 *
 * @noinspection PhpUnused
 */
class Roles extends Controller {

    private RoleModel $roleModel;

    protected ValidateModel $validateModel;


    /**
     * Constructs a new instance of the class.
     *
     * This method initializes a new instance of the class by creating a new `Database` object with the provided
     * database host, name, user, and password. It then creates a new `RoleModel` object using the created
     * `Database` object and assigns it to the `roleModel` property.
     *
     * Additionally, it creates a new `ValidateModel` object using the `roleModel` as a dependency and assigns
     * it to the `validateModel` property.
     *
     * @noinspection PhpUnused
     */
    public function __construct() {
        $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->roleModel = new RoleModel($database);
        $this->validateModel = new ValidateModel($this->roleModel);
    }

    /**
     * Index method
     *
     * This method retrieves all roles from the database and returns a response.
     *
     * @return Response The response containing the roles
     */
    public function index(): Response {
        $roles = $this->roleModel->findAll();
        return $this->view(template: "Roles/index.tlmt", data: [
            "roles" => $roles
        ]);
    }

    /**
     * Create a new role.
     *
     * This method returns a Response object with the view template "Roles/create.tlmt".
     *
     * @return Response The response with the view template for creating a new role.
     *
     * @noinspection PhpUnused
     */
    public function create(): Response {
        return $this->view(template: "Roles/create.tlmt");
    }

    /**
     * Create a role.
     *
     * Validates the POST request and creates a new role based on the provided data.
     * If the request is not valid, an error view is returned.
     *
     * @return Response Returns the response of the createRole method.
     *
     * @noinspection PhpUnused
     */
    public function createRole(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"]);
        }
        if ($this->validateModel->validateData(data: $_POST)) {
            $this->createRol(postData: $_POST);
        } else {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Role Entry Error",
                "message" => "Invalid Information Entered",
                "errorcode" => "406"]);
        }
        return $this->index();
    }

    /**
     * Update method for the class.
     *
     * This method retrieves a role with the given ID from the roleModel. If the role is found, it returns a view with
     * the update form pre-filled with the role's data. If the role is not found, it returns an error view.
     *
     * @param string $id The ID of the role to update.
     *
     * @return Response The response object that represents the view.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id): Response {
        $old_dep = $this->roleModel->find($id);
        if(!$old_dep) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Role Entry Error",
                "message" => "Record not found",
                "errorcode" => "404"
            ]);
        } else {
            return $this->view(template: 'Roles/update.tlmt', data: [
                "colId" => $old_dep['colId'],
                "colName" => $old_dep['colName']
            ]);
        }
    }

    /**
     * Update the role.
     *
     * This method is used to update the role. It first verifies if the request is a POST request,
     * and if it is not, it returns an error response. It then validates the data from the POST request.
     * If the data is valid, it calls the `updateRecord` method on the roleModel instance to update the record.
     * If the record update is successful, it returns an error response. Otherwise, it returns the index view.
     *
     * @return Response The response object representing the updated role or an error message.
     *
     * @noinspection PhpUnused
     */
    public function updateRole(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST)) {
            if($this->roleModel->updateRecord($_POST)) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Save Error",
                    "message" => "Unable to update role",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }

    /**
     * Deletes a record by ID.
     *
     * This method finds the record with the given ID in the RoleModel and deletes it.
     * If the record is not found, it returns a 404 error view.
     * Otherwise, it returns a view with the record's "colId" and "colName" values.
     *
     * @param string $id The ID of the record to delete.
     *
     * @return Response The response containing the view data.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): Response {
        $oldDep = $this->roleModel->find(id: $id);
        if(!$oldDep) {
            return $this->view(template: "blank.tlmt", data: [
                "title" => "Missing record",
                "message" => "Unable to locate record",
                "errorcode" => "404"
            ]);
        }
        $colId = $oldDep['colId'];
        $colName = $oldDep['colName'];
        return $this->view(template: 'Roles/delete.tlmt', data: [
            "colId" => $colId,
            "colName" => $colName
        ]);
    }

    /**
     * Deletes a role.
     *
     * This method is responsible for deleting a role based on the data received from a POST request.
     * It verifies if the request is a POST request and if the data is valid for the delete operation.
     * If the request is not a POST request, a Method Not Allowed response will be returned.
     * If the data is valid, the role will be deleted using the `delete()` method of the `roleModel` instance.
     * If the deletion is successful, the index page will be returned.
     * If the deletion fails, an Error response with a code of 500 will be returned.
     *
     * @return Response The response object containing the index page or error page.
     *
     * @noinspection PhpUnused
     */
    public function deleteRole(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST, op: "delete")) {
            if(!$this->roleModel->delete($_POST["colId"])) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Deletion Error",
                    "message" => "Unable to delete role",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }

    /**
     * Creates a new role entry using the provided post data.
     *
     * @param array $postData The post data containing the role information.
     *
     * @return void
     */
    private function createRol(array $postData): void {
        if (!$this->roleModel->insert($postData)) {
            $this->view(template: "error.tlmt", data: [
                "title" => "Role Entry Error",
                "message" => "Unable to save role",
                "code" => "500"
            ]);
        }
    }
}