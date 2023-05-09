<?php

namespace App\Filters;

use App\Controllers\BaseController;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;

class AuthFilter implements FilterInterface {

    private $algorithm = "HS256";

    public function before(RequestInterface $request, $arguments = null) {

        // Vérifier si le header Authorization est présent
        $authHeader = $request->header("Authorization");
        if (!isset($authHeader)) {
            return redirect()->to("/unauthorized");
        }

        // Vérifier si le header Authorization est au format Bearer en utilisant preg_match
        if (!preg_match("/^Bearer\s/", $authHeader)) {
            return redirect()->to("/unauthorized");
        }

        // Vérifier si le token est présent
        $token = explode(" ", $authHeader)[1];
        if (!$token) {
            return redirect()->to("/unauthorized");
        }

        try {
            // Décoder le token JWT et vérifier si il n'est pas expiré
            $decoded = JWT::decode($token, getenv("JWT_SECRET"), array($this->algorithm));
            if ($decoded->exp < time()) { // Token expiré
                return redirect()->to("/unauthorized");
            }

            // Ajouter les informations de l'utilisateur dans la requête
            $request->user = $decoded->data;
        } catch (\Exception $e) {
            return redirect()->to("/unauthorized");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
    }
}
