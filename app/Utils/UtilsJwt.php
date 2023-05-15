<?php

namespace App\Utils;

class UtilsJwt {
    private $data;

    public function setTokenData($data) {
        $this->data = $data;
    }

    public function getTokenData() {
        return $this->data;
    }
}