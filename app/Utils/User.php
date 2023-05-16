<?php

namespace App\Utils;

class User {
    private $id;
    private $idRole;
    private $idFoyer;

    public function __construct(int $id, int $idRole, int $idFoyer) {
        $this->id     = $id;
        $this->idRole = $idRole;
        $this->idFoyer = $idFoyer;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getIdFoyer(): int {
        return $this->idFoyer;
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