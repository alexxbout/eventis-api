<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface {

    private $algorithm = "HS256";

    public function before(RequestInterface $request, $arguments = null) {
        /**
         * Format du header : Authorization: Bearer <token>
         */

        // Vérifier si le header Authorization est présent
        $header = $request->header("Authorization");
        if (!isset($header)) {
            return redirect()->to("/unauthorized");
        }

        // Vérifier si le header Authorization est au format Bearer
        $authHeader = explode(" ", $header);
        if (count($authHeader) !== 3 || $authHeader[1] !== "Bearer") {
            return redirect()->to("/unauthorized");
        }

        // Vérifier si le token est présent
        $token = $authHeader[2];
        if (!$token) {
            return redirect()->to("/unauthorized");
        }

        try {
            // Décoder le token JWT et vérifier si il n'est pas expiré
            $decoded = JWT::decode($token, new Key(getenv("JWT_SECRET"), $this->algorithm));
            if ($decoded->exp < time()) { // Token expiré
                return redirect()->to("/unauthorized");
            }

            // Ajouter les informations de l'utilisateur dans le service jwt
            service("jwt")->setTokenData($decoded->data);
        } catch (\Exception $e) {
            return redirect()->to("/unauthorized");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
    }
}
