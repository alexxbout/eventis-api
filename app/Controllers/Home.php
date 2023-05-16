<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

class Home extends BaseController {

    public function index() {
        $this->send(HTTPCodes::OK, null, "API is working");
    }

    public function unauthorized() {
        $this->send(HTTPCodes::UNAUTHORIZED, null, "Authentication required");
    }
}
