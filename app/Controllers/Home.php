<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

class Home extends BaseController {

    private const API_WORKING  = "L'API fonctionne !";
    private const UNAUTHORIZED = "Authentication requise";

    public function index() {
        $this->send(HTTPCodes::OK, null, self::API_WORKING);
    }

    public function unauthorized() {
        $this->send(HTTPCodes::UNAUTHORIZED, null, self::UNAUTHORIZED);
    }
}
