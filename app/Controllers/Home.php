<?php

namespace App\Controllers;

use App\Utils\HTTPCodes;

class Home extends BaseController {

    public function index(): void {
        $this->send(HTTPCodes::OK, null, "API is working");
    }

    public function unauthorized(): void {
        $this->send(HTTPCodes::UNAUTHORIZED, ["message" => "Authentication required"]);
    }
}
