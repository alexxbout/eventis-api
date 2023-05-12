<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Utils\HTTPCodes;
use Firebase\JWT\JWT;
use Psr\Log\LoggerInterface;

class AuthController extends BaseController {

    private $expiration_prod = 60 * 60 * 24; // 24h
    private $expiration_dev = 60 * 60 * 24 * 7; // 1 week
    private $algorithm = "HS256";

    private $userModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->userModel = new UserModel();
    }

    public function login(): void {
        /**
         * Format du header : Authorization: Bearer <token>
         */

        // Vérifier si le header Authorization est présent
        $header = $this->request->header("Authorization");
        if (!isset($header)) {
            // Renvoyer une erreur 401 Unauthorized avec le header WWW-Authenticate pour indiquer à l'utilisateur comment s'authentifier
            $this->send(HTTPCodes::UNAUTHORIZED, null, "No authorization header", null, ["WWW-Authenticate" => "Bearer"]);
            return;
        }

        // Vérifier si le header Authorization est au format Bearer
        $authHeader = explode(" ", $header);
        if (count($authHeader) !== 3 || $authHeader[1] !== "Bearer") {
            $this->send(HTTPCodes::UNAUTHORIZED, null, "Wrong authorization header format");
            return;
        }

        // Vérifier si le token est présent
        $token = $authHeader[2];
        if (!$token) {
            $this->send(HTTPCodes::UNAUTHORIZED, null, "No token");
            return;
        }

        // Décoder le login et le mot de passe
        $decoded = base64_decode($token);

        // Récupérer le login et le mot de passe
        $login = explode(":", $decoded)[0];
        $password = explode(":", $decoded)[1];

        // Vérifier si le login existe
        $user = $this->userModel->getByLogin($login);

        if ($user == null) {
            $this->send(HTTPCodes::UNAUTHORIZED, null, "User not found");
            return;
        }

        // Vérifier si le mot de passe est correct
        if (!password_verify($password, $user->password)) {
            $this->send(HTTPCodes::UNAUTHORIZED, null, "Wrong password");
            return;
        }

        // Mettre à jour lastLogin
        // XXX

        // Expiration du token : 24h en production, 1 semaine en développement
        $exp = getenv("CI_ENVIRONMENT") === "production" ? $this->expiration_prod : $this->expiration_dev;

        // Générer un token JWT
        $payload = [
            "iss" => base_url(),                 // Issuer : used by the recipient of a JWT to verify the issuer
            "aud" => base_url(),                 // Audience : used by the recipient of a JWT to identify the intended recipient
            "iat" => time(),                     // Issued at : contains the timestamp at which the token was issued
            "exp" => time() + $exp, // Expiration time : contains the timestamp at which the token will expire
            "data" => [ // Data : contains the claims. Don't put sensitive data here, it will be visible by anyone.
                "id"        => $user->id,
                "login"     => $user->login,
                "idRole"    => $user->idRole
            ]
        ];

        // Signer le token avec le secret
        // Le token sera vérifié avec le secret par le filtre JWTFilter pour chaque requête protégée
        $token = JWT::encode($payload, getenv("JWT_SECRET"), $this->algorithm);

        // Envoyer la réponse et le token
        // Le client devra stocker le token dans le local storage
        // Le client devra envoyer le token dans le header Authorization pour chaque requête
        // Le token sera vérifié par le filtre JWTFilter pour chaque requête protégée
        $this->send(HTTPCodes::OK, ["user" => $user, "token" => $token], "User logged in");
    }
}
