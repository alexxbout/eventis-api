<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use CodeIgniter\Model;
use Psr\Log\LoggerInterface;
use DateTime;

class CodeController extends BaseController {

    private const CODE_LENGTH = 6;
    private $codeModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->codeModel = new \App\Models\CodeModel();
    }

    public function getAll(): void {
        $data = $this->codeModel->getAll();
        if ($data != null) {
            // Ajouter l'information de validité pour chaque code
            foreach ($data as &$code) {
                $code["valid"] = $this->isValid($this->codeModel, $code["id"]);
            }
            $this->send(HTTPCodes::OK, $data, "OK");
        } else {
            $this->send(HTTPCodes::NO_CONTENT);
        }
    }

    public function checkExists(string $code): void {
        $exists = $this->codeModel->checkExists($code);

        if ($exists) {
            $data = $this->codeModel->getByCode($code);
            
            $valid = $this->isValid($this->codeModel, $data["id"]);

            $this->send(HTTPCodes::OK, ["exists" => $exists, "valid" => $valid], "OK");
        } else {
            $this->send(HTTPCodes::OK, ["exists" => $exists, "valid" => false], "OK");
        }
    }
    
    public function generate(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "idFoyer" => "required|integer",
            "expire" => "required|valid_date[Y-m-d H:i:s]" // On devrait limiter la date d'expiation dans le code, pour éviter que les gens ne mettent des dates trop lointaines.
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, null, "Validation error", $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $code = "";

            // Générer un code unique
            do {
                $code = $this->randomCode();
            } while ($this->codeModel->checkExists($code));

            $id = $this->codeModel->add($code, $data["idFoyer"], $data["expire"]);
        
            $this->send(HTTPCodes::OK, ["id" => $id, "code" => $code], "Code generated");
        }
    }

    /**
     * It generates a random string of CODE_LENGTH characters
     * 
     * @return string A string of CODE_LENGTH random characters.
     */
    private function randomCode(): string {
        $code = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < CodeController::CODE_LENGTH; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $code;
    }

    /**
     * This function checks if a code is valid by checking if it's not expired and not used
     * 
     * @param int id The id of the code to check
     * 
     * @return bool a boolean value.
     */
    public static function isValid(Model $codeModel, int $id): bool {
        $data = $codeModel->getById($id);
        if ($data == null) {
            return false;
        }
        $expire = new DateTime($data["expire"]);
        return $expire > new DateTime() && !boolval($data["used"]);
    }
}
