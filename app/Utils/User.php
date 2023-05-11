<?php

namespace App\Utils;

class User {
    private $id;
    private $idRole;

    public function __construct(int $id, int $idRole) {
        $this->id     = $id;
        $this->idRole = $idRole;
    }

    public function getId(): int {
        return $this->id;
    }

    public function isAdmin(): bool {
        return $this->idRole === 3;
    }

    public function isDeveloper(): bool {
        return $this->idRole === 0;
    }

    public function isEducator(): bool {
        return $this->idRole === 1;
    }

    public function isUser(): bool {
        return $this->idRole === 2;
    }
}