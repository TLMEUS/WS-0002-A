<?php
/**
 * This file contains the src/App/Controllers/Applications.php file for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Controllers
 * File Name: Applications.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
declare(strict_types=1);

namespace App\Controllers;

use App\Database;
use App\Models\ApplicationModel;
use App\Models\ValidateModel;
use Framework\Controller;
use Framework\Response;

/**
 * Class Applications
 *
 * Represents a controller class for managing applications.
 *
 * @noinspection PhpUnused
 */
class Applications extends Controller
{
    private ApplicationModel $applicationModel;
    protected ValidateModel $validateModel;

    /**
     * Constructs a new instance of the class.
     *
     * This method initializes a new instance of the class by creating a new `Database` object with the provided
     * database host, name, user, and password. It then creates a new `ApplicationModel` object using the created
     * `Database` object and assigns it to the `applicationModel` property.
     *
     * Additionally, it creates a new `ValidateModel` object using the `applicationModel` as a dependency and assigns
     * it to the `validateModel` property.
     *
     * @noinspection PhpUnused
     */
    public function __construct() {
        $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->applicationModel = new ApplicationModel($database);
        $this->validateModel = new ValidateModel($this->applicationModel);
    }

    /**
     * Retrieves all the applications from the database and returns a Response object
     * containing the rendered view.
     *
     * @return Response The response for the index page.
     *
     * @noinspection PhpUnused
     */
    public function index(): Response {
        $applications = $this->applicationModel->findAll();
        return $this->view(template: 'Applications/index.tlmt', data: [
            'applications' => $applications
        ]);
    }

    /**
     * Creates a new application.
     *
     * This method returns a Response object which contains the view template for creating a new application.
     *
     * @return Response The Response object containing the view template for creating a new application.
     *
     * @noinspection PhpUnused
     */
    public function create(): Response {
        return $this->view(template: "Applications/create.tlmt");
    }

    /**
     * Creates a new application.
     *
     * Verifies if the request is a POST request and returns an error response if it is not.
     * Validates the data received in the POST request and returns an error response if the data is invalid.
     * Calls the `applicationModel->insert` method to create the application with the validated data.
     * Returns a response with the index page.
     *
     * @return Response The response object containing the index page.
     *
     * @noinspection PhpUnused
     */
    public function createApplication(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"]);
        }
        if ($this->validateModel->validateData($_POST)) {
            if (!$this->applicationModel->insert($_POST)) {
                $this->view(template: "error.tlmt", data: [
                    "title" => "Application Entry Error",
                    "message" => "Unable to save application",
                    "errorcode" => "500"
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
     * Updates an application entry with the given ID.
     *
     * @param string $id The ID of the application entry to be updated.
     *
     * @return Response The response object.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id): Response {
        $appData = $this->applicationModel->find($id);
        if(!$appData) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Department Entry Error",
                "message" => "Record not found",
                "errorcode" => "404"
            ]);
        } else {
            return $this->view(template: 'Applications/update.tlmt', data: [
                "colId" => $appData['colId'],
                "colName" => $appData['colName'],
                "colDomain" => $appData['colDomain']
            ]);
        }
    }


    /**
     * Updates the application data and returns a response.
     *
     * This method first validates the POST data using the `validateModel` object.
     * If the validation fails, an error response is returned with the error message and code.
     *
     * If the data validation succeeds, the `update()` method of the `applicationModel` object is called
     * with the specified `id` and `data` from the POST request. If the update fails, an error response
     * is returned with the error message and code.
     *
     * If the update is successful or the data validation fails, the `index()` method is called and its
     * returned response is returned by this method.
     *
     * @return Response The response object.
     *
     * @noinspection PhpUnused
     */
    public function updateApplication(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST, op: "UAPP")) {
            if($this->applicationModel->updateRecord(data: $_POST)) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Save Error",
                    "message" => "Unable to update application",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }

    /**
     * Deletes an application.
     *
     * Retrieves the application with the specified ID using the `applicationModel->find` method.
     * Returns a response that renders the delete application view and passes the application data as parameters.
     *
     * @param string $id The ID of the application to be deleted.
     *
     * @return Response The response object containing the delete application view.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): Response {
        $old_app = $this->applicationModel->find($id);
        if(!$old_app) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Delete User Error",
                "message" => "Unable to locate user",
                "errorcode" => "404"
            ]);
        }
        return $this->view(template: 'Applications/delete.tlmt', data: [
            "colId" => $old_app['colId'],
            "colName" => $old_app['colName']
        ]);
    }


    /**
     * Deletes an application by ID.
     *
     * Verifies if the request is a POST request and returns an error response if it is not.
     * Validates the data received in the POST request for deletion operation and sets the appropriate operation flag.
     * Calls the `applicationModel->delete` method to delete the application with the given ID.
     * Returns a response with the index page.
     *
     * @return Response The response object containing the index page.
     *
     * @noinspection PhpUnused
     */
    public function deleteApplication(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        $this->validateModel->validateData($_POST, op: "delete");
        if(!$this->applicationModel->delete($_POST['colId'])) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Delete Application Error",
                "message" => "Unable to delete application",
                "errorcode" => "500"
            ]);
        }
        return $this->index();
    }
}