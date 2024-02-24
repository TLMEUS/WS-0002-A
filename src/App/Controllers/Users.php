<?php
/**
 * This file contains the src/App/Controllers/Users.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Controllers
 * File Name: Users.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
namespace App\Controllers;

use App\Database;
use App\Models\DepartmentModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\ValidateModel;
use Framework\Controller;
use Framework\Response;

/**
 * Class Users
 *
 * Represents a class for managing users.
 *
 * @noinspection PhpUnused
 */
class Users extends Controller {

    private UserModel $userModel;

    private DepartmentModel $departmentModel;

    private RoleModel $roleModel;

    protected ValidateModel $validateModel;

    /**
     * MyClass constructor.
     *
     * Instantiates a new Database object using the provided environment variables.
     * Instantiates a new UserModel object with the Database object as a parameter.
     */
    public function __construct() {
        $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->userModel = new UserModel($database);
        $this->departmentModel = new DepartmentModel($database);
        $this->roleModel = new RoleModel($database);
        $this->validateModel = new ValidateModel($this->userModel);
    }

    /**
     * Index method.
     *
     * Fetches all users from the UserModel.
     * Renders a view using the 'Users/index.tlmt' template and passes the users data as a parameter.
     *
     * @return Response The rendered view.
     *
     * @noinspection PhpUnused
     */
    public function index(): Response {
        $users = $this->userModel->findAll();
        return $this->view(template: 'Users/index.tlmt', data: ['users' => $users]);
    }

    /**
     * Create a new user.
     *
     * This method retrieves all departments and roles from the database and passes them to the 'Users/create.tlmt'
     * view template along with an empty data array.
     *
     * @return Response The response generated by the 'Users/create.tlmt' view template, containing the retrieved
     * departments and roles.
     *
     * @noinspection PhpUnused
     */
    public function create(): Response {
        $departments = $this->departmentModel->findAll();
        $roles = $this->roleModel->findAll();
        return $this->view(template: 'Users/create.tlmt', data: ['departments' => $departments, 'roles' => $roles]);
    }

    /**
     * Creates a new user and returns a Response object.
     *
     * Validates the POST data using the ValidateModel object. If the data is not valid,
     * it returns an error response with an error template, title, message, and error code.
     *
     * If the data is valid, it calls the createEntity method with the POST data to create
     * a new user entity. If successful, it returns the index response.
     *
     * @return Response Returns a Response object
     *
     * @noinspection PhpUnused
     */
    public function createUser(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"]);
        }
        if ($this->validateModel->validateData(data: $_POST)) {
            $this->userModel->insert(data: $_POST);
        } else {
            return $this->view(template: "error.tlmt", data: [
                "title" => "User Entry Error",
                "message" => "Invalid Information Entered",
                "errorcode" => "406"
            ]);
        }
        return $this->index();
    }

    /**
     * Updates a user record based on the provided ID.
     *
     * Retrieves the old user record using the UserModel's `find` method with the provided ID.
     * Retrieves all the departments using the DepartmentModel's `findAll` method.
     * Retrieves all the roles using the RoleModel's `findAll` method.
     *
     * If the old user record is not found, it returns an error response view with the following data:
     *     - "title": A string representing the error title
     *     - "message": A string representing the error message
     *     - "errorcode": A string representing the error code
     *
     * Returns a view with the following data:
     *     - "user": The old user record
     *     - "departments": All the departments
     *     - "roles": All the roles
     *
     * @param string $id The ID of the user to update.
     *
     * @return Response The response view containing the user record, departments, and roles.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id): Response {
        $old_user = $this->userModel->find($id);
        $departments = $this->departmentModel->findAll();
        $roles = $this->roleModel->findAll();
        if(!$old_user) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Update User Error",
                "message" => "Unable to locate user",
                "errorcode" => "404"
            ]);
        }
        return $this->view(template: 'Users/update.tlmt', data: [
            "user" => $old_user,
            "departments" => $departments,
            "roles" => $roles
        ]);
    }

    /**
     * Updates a user.
     *
     * This method updates a user using the $_POST data. It performs the following steps:
     * 1. Validates the request method. If it's not a POST request, it returns an error response.
     * 2. Dumps the $_POST data for debugging purposes.
     * 3. Validates the $_POST data using the "update" operation.
     * 4. Updates the user in the userModel with the provided "colId" and $_POST data.
     *    If the update fails, it returns an error response.
     * 5. Returns the index page.
     *
     * @return Response The response containing the result of the update operation or an error response.
     *
     * @noinspection PhpUnused
     */
    public function updateUser(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        var_dump($_POST);
        if ($this->validateModel->validateData($_POST, op: "update")) {
            if (!$this->userModel->update(id: $_POST['colId'], data: $_POST)) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Update Error",
                    "message" => "Unable to update user",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }

    /**
     * Delete method.
     *
     * Finds the user with the specified ID using the UserModel's find method.
     * If the user is not found, it returns an error view with a 404 status code.
     * If the user is found, it returns a delete view with the user's ID and username.
     *
     * @param string $id The ID of the user to be deleted.
     *
     * @return Response The response object.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): Response {
        $old_user = $this->userModel->find($id);
        if(!$old_user) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Delete User Error",
                "message" => "Unable to locate user",
                "errorcode" => "404"
            ]);
        }
        return $this->view(template: 'Users/delete.tlmt', data: [
            "colId" => $old_user['colId'],
            "colUname" => $old_user['colUName']
        ]);
    }

    /**
     * Deletes a user.
     *
     * This method deletes a user using the $_POST data. It performs the following steps:
     * 1. Validates the request method. If it's not a POST request, it returns an error response.
     * 2. Validates the $_POST data using the "delete" operation.
     * 3. Deletes the user in the userModel with the provided "colId".
     *    If the deletion fails, it returns an error response.
     * 4. Returns the index page.
     *
     * @return Response The response containing the result of the deletion or an error response.
     *
     * @noinspection PhpUnused
     */
    public function deleteUser(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        $this->validateModel->validateData($_POST, op: "delete");
        if(!$this->userModel->delete($_POST['colId'])) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Delete User Error",
                "message" => "Unable to delete user",
                "errorcode" => "500"
            ]);
        }
        return $this->index();
    }
}