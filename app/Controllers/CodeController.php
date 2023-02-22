<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

class CodeController extends BaseController {

    private const CODE_LENGTH = 6;
    private $codeModel;

    public function __construct() {
        $this->codeModel = new \App\Models\CodeModel();
    }

    public function getAll(): void {
        $data = $this->codeModel->getAll();
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NO_CONTENT, []);
        }
    }

    public function getById(int $id): void {
        $data = $this->codeModel->getById($id);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NOTFOUND, []);
        }
    }

    public function getByIdFoyer(int $idFoyer): void {
        $data = $this->codeModel->getByIdFoyer($idFoyer);
        if ($data != null) {
            $this->send(HTTPCodes::OK, $data);
        } else {
            $this->send(HTTPCodes::NOTFOUND, []);
        }
    }

    public function checkExists(string $code): void {
        if ($this->codeModel->checkExists($code)) {
            $this->send(HTTPCodes::OK, ["message" => "Code existant"]);
        } else {
            $this->send(HTTPCodes::NO_CONTENT, ["message" => "Code inexistant"]);
        }
    }
    
    public function generate(): void {
        $validation =  \Config\Services::validation();

        $validation->setRules([
            "idFoyer" => "required|integer",
            "expire" => "required|valid_date[Y-m-d H:i:s]" // On devrait limiter la date d'expiation dans le code, pour éviter que les gens ne mettent des dates trop lointaines.
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $this->send(HTTPCodes::BAD_REQUEST, $validation->getErrors());
        } else {
            $data = $this->request->getJSON(true);

            $code = "";

            // Generer un code unique
            do {
                $code = $this->generateCode();
            } while ($this->codeModel->checkExists($code));

            $this->codeModel->add($code, $data["idFoyer"], $data["expire"]);
            // Ici on devrait vérifier que l'insertion s'est bien passée, et renvoyer une erreur si ce n'est pas le cas.
        
            $this->send(HTTPCodes::OK, ["message" => "Code generated successfully", "code" => $code]);
        }
    }

    /**
     * It generates a random string of CODE_LENGTH characters
     * 
     * @return string A string of CODE_LENGTH random characters.
     */
    private function generateCode(): string {
        $code = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < CodeController::CODE_LENGTH; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $code;
    }
}
