<?php
/**
 * This file contains the src/AppControllers/UserAccess.php file for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Controllers
 * File Name: UserAccess.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
namespace App\Controllers;

use App\Database;
use App\Models\ApplicationModel;
use App\Models\UserAccessModel;
use App\Models\UserModel;
use App\Models\ValidateModel;
use Framework\Controller;
use Framework\Response;
use PDOException;

/**
 * UserAccess Class
 *
 * This class represents the UserAccess controller.
 * It is responsible for handling all the user access permissions to the application in the system.
 *
 * @package     App
 * @subpackage  Controllers
 *
 * @noinspection PhpUnused
 */
class UserAccess extends Controller {

    protected UserModel $userModel;
    protected ApplicationModel $applicationModel;
    private UserAccessModel $userAccessModel;
    protected ValidateModel $validateModel;

    /**
     * Constructs a new instance of the class.
     *
     * This method initializes a new instance of the class by creating a new `Database` object with the provided
     * database host, name, user, and password. It then creates a new `UserAccessModel` object using the created
     * `Database` object and assigns it to the `userAccessModel` property.
     *
     * Additionally, it creates a new `ValidateModel` object using the `userAccessModel` as a dependency and assigns
     * it to the `validateModel` property.
     *
     * @noinspection PhpUnused
     */
    public function __construct() {
        $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->userModel = new UserModel($database);
        $this->applicationModel = new ApplicationModel($database);
        $this->userAccessModel = new UserAccessModel($database);
        $this->validateModel = new ValidateModel($this->userAccessModel);
    }

    /**
     * Get the index page.
     *
     * This method retrieves the user access list from the userAccessModel and returns the index page.
     *
     * @return Response The response object representing the index page.
     *
     * @noinspection PhpUnused
     */
    public function index(): Response {
        $accesslist = $this->userAccessModel->findAll();
        return $this->view(template: 'UserAccess/index.tlmt', data: [
            'accesslist' => $accesslist
        ]);
    }

    /**
     * Create role access.
     *
     * This method retrieves all users and applications from their respective models and returns a view
     * for creating a new role access. It performs the following steps:
     * - Retrieves all users using the UserModel's findAll() method.
     * - Retrieves all applications using the ApplicationModel's findAll() method.
     * - Returns the view for creating a new role access, passing the retrieved users and applications as data.
     *
     * @return Response The response object representing the view for creating a new role access.
     *
     * @noinspection PhpUnused
     */
    public function create(): Response {
        $users = $this->userModel->findAll();
        $applications = $this->applicationModel->findAll();
        return $this->view(template: 'UserAccess/create.tlmt', data: [
            'users' => $users,
            'applications' => $applications
        ]);
    }

    /**
     * Create user access.
     *
     * This method creates a user access record based on the provided data. It performs the following steps:
     * - Validates the request method. If it is not a POST request, it returns an error response.
     * - Validates the data sent in the request. If the data is invalid, it returns an error response.
     * - Inserts the user access record using the validated data. If the insert is unsuccessful, it returns an error response.
     * - If all steps are successful, it returns the index page.
     *
     * @return Response The response object representing the index page.
     *
     * @noinspection PhpUnused
     */
    public function createUserAccess(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"]);
        }
        if ($this->validateModel->validateData($_POST, op: 'UAR')) {
            try {
                $this->userAccessModel->insert($_POST);
            } catch (PDOException $ex) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "User Access Record Entry Error",
                    "message" => $ex->getMessage(),
                    "errorcode" => "500"
                ]);
            }
        } else {
            return $this->view(template: "error.tlmt", data: [
                "title" => "User Access Record Entry Error",
                "message" => "Invalid Information Entered",
                "errorcode" => "406"
            ]);
        }
        return $this->index();
    }

    /**
     * Update user access.
     *
     * This method retrieves the existing user access record based on the provided ID and prepares the data to update
     * the record. It performs the following steps:
     * - Searches for the user access record with the given ID. If the record is not found, it returns an error
     *   response.
     * - If the record is found, it retrieves the user and application data associated with the record.
     * - It returns a view with the updated user access data and associated details.
     *
     * @param string $id The ID of the user access record to update.
     *
     * @return Response The response object representing the view with the updated user access data and
     * associated details.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id): Response {
        $old = $this->userAccessModel->find($id);
        if(!$old) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "User Access Record Entry Error",
                "message" => "Record not found",
                "errorcode" => "404"
            ]);
        } else {
            $user = $this->userModel->find($old['colName']);
            $application = $this->applicationModel->find($old['colApplication']);
            return $this->view(template: 'UserAccess/update.tlmt', data: [
                "colId" => $old['colId'],
                "colUName" => $user['colUName'],
                "colAccess" => $old['colAccess']
            ]);
        }
    }

    /**
     * Update user access.
     *
     * This method updates an existing user access record based on the provided data. It performs the following steps:
     * - Validates the request method. If it is not a POST request, it returns an error response.
     * - Validates the data sent in the request. If the data is invalid, it returns an error response.
     * - Updates the user access record with the provided id using the validated data. If the update is unsuccessful,
     *   it returns an error response.
     * - If all steps are successful, it returns the index page.
     *
     * @return Response The response object representing the index page.
     *
     * @noinspection PhpUnused
     */
    public function updateUserAccess(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST, op: "UAR")) {
            if($this->userAccessModel->updateRecord(data: $_POST)) {
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
     * Delete user access.
     *
     * This method deletes a user access record based on the provided ID. It performs the following steps:
     * - Retrieves the old user access record using the provided ID. If the record does not exist, it returns an error response.
     * - Retrieves additional information about the user and application associated with the old user access record.
     * - Returns a view for deleting the user access record, including the ID, username, and application name.
     *
     * @param string $id The ID of the user access record to be deleted.
     * @return Response The response object representing the delete user access page.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): Response {
        $old = $this->userAccessModel->find($id);
        if(!$old) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Delete Record Error",
                "message" => "Unable to locate record",
                "errorcode" => "404"
            ]);
        }
        $user = $this->userModel->find($old['colName']);
        $application = $this->applicationModel->find($old['colApplication']);
        return $this->view(template: 'UserAccess/delete.tlmt', data: [
            "colId" => $old['colId'],
            "colName" => $user['colUName'],
            "colApplication" => $application['colName'],
        ]);
    }

    /**
     * Delete a record.
     *
     * This method deletes a record based on the provided data. It performs the following steps:
     * - Validates the request method. If it is not a POST request, it returns an error response.
     * - Validates the data sent in the request. If the data is invalid, it continues execution without
     *   throwing an error.
     * - Deletes the record specified by the "colId" key in the $_POST array. If the delete operation is unsuccessful,
     *   it returns an error response.
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
        if(!$this->userAccessModel->delete($_POST['colId'])) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Delete Record Error",
                "message" => "Unable to delete record",
                "errorcode" => "500"
            ]);
        }
        return $this->index();
    }
}