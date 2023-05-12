<?php

namespace App\Controllers;

use App\ThirdParty\TokenService;
use App\Utils\HTTPCodes;
use App\Utils\User;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller {
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    protected User $user;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        $data = $this->getJwtData();

        if (isset($data)) {
            $this->user = new User($data->id, $data->idRole);
        }
    }

    /**
     * It sends a JSON response to the client
     * 
     * @param int status_code The HTTP status code to send.
     * @param array|null data The data to be sent to the client.
     * @param string message The message to be displayed to the client.
     * @param string|array|null errors An array of errors.
     * @param array header An array of headers to be sent with the response.
     */
    protected function send(int $status_code, stdClass|array|null $data = null, string $message = "", string|array|null $errors = null, array $headers = []): void {
        foreach ($headers as $header => $value) {
            $this->response->setHeader($header, $value);
        }

        $json          = new stdClass();
        $json->status  = $status_code;
        $json->message = $message;
        $json->data    = $data;

        if ($errors !== null) {
            $json->errors = $errors;
        }

        $this->response
            ->setContentType("application/json")
            ->setStatusCode($status_code)
            ->setJson($json);

        $this->response->send();
    }

    /**
     * This function retrieves the JWT data and returns it.
     * 
     * @return object data obtained from the JWT token.
     */
    private function getJwtData(): object | null {
        return service("jwt")->getTokenData();
    }
}
