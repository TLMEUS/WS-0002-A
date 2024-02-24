<?php
/**
 * This file contains the src/App/Controllers/Home.php class for project WS-0002-A
 *
 * File Information:
 * Project Name: WS-0002-A
 * Module Name: Source
 * Group Name: App
 * Section Name: Controllers
 * File Name: Home.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 02/2023
 */
declare(strict_types=1);

namespace App\Controllers {

    use App\Database;
    use App\Models\ApplicationModel;
    use App\Models\UserAccessModel;
    use Framework\Controller;
    use Framework\Response;
    use App\Models\UserModel;

    /**
     * Class Home
     *
     * This class represents the Home controller.
     *
     * @noinspection PhpUnused
     */
    class Home extends Controller {

        protected UserModel $userModel;
        protected ApplicationModel $applicationModel;
        protected UserAccessModel $userAccessModel;

        /**
         * Constructs a new object of the class.
         *
         * Initializes a new instance of the class by creating a new Database object which is used for database connections.
         * It also initializes the $homeModel and $applicationModel properties using the newly created Database object.
         *
         * @return void
         *
         * @noinspection PhpUnused
         */
        public function __construct() {
            $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
            //$this->userModel = new UserModel($database);
            //$this->applicationModel = new ApplicationModel($database);
            //$this->userAccessModel = new UserAccessModel($database);
        }

        /**
         * Renders the index page of the Home controller.
         *
         * This method renders the index page of the Home controller by returning a Response object. The index page
         * template is specified as "Home/index.tlmt" and the data passed to the template
         * includes the given `$app` variable.
         *
         * @return Response The Response object representing the index page view.
         *
         * @noinspection PhpUnused
         */
        public function index(): Response {
            return $this->view(template: "Home/index.tlmt");
        }

        /**
         * Display the "no access" page.
         *
         * @return Response Returns a Response object representing the "no access" page.
         *
         * @noinspection PhpUnused
         */
        public function noaccess(): Response {
            return $this->view(template: "Home/noaccess.tlmt");
        }

        /**
         * Denies access to the user and returns a Response object.
         *
         * This method is used to deny access to the user and returns a Response object containing the rendered template
         * "Home/denyaccess.tlmt".
         *
         * @return Response The Response object containing the rendered template.
         *
         * @noinspection PhpUnused
         */
        public function denyaccess(): Response {
            return $this->view(template: "Home/denyaccess.tlmt");
        }
    }
}