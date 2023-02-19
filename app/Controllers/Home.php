<?php

namespace App\Controllers;

class Home extends BaseController {
    public function index(): void {
        $this->send(200, ["message" => "API is working"]);
    }
}
