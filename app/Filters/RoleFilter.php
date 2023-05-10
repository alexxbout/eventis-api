<?php

namespace App\Filters;

use App\Controllers\BaseController;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;

class RoleFilter implements FilterInterface {

    public function before(RequestInterface $request, $arguments = null) {
        // PAS SUR SI BESOIN Vérifier si le token est présent
        $token = explode(" ", $authHeader)[1];
        if (!$token) {
            return redirect()->to("/unauthorized");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
    }
}