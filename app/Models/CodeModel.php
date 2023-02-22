<?php

namespace App\Models;

use DateTime;

class CodeModel extends BaseModel {

    public function getAll(): array|null {
        return $this->db->table("code")->get()->getResultArray();
    }

    public function getById(int $id): array|null {
        return $this->db->table("code")->getWhere(["id" => $id])->getRowArray();
    }

    public function getByIdFoyer(int $idFoyer): array|null {
        return $this->db->table("code")->getWhere(["idFoyer" => $idFoyer])->getRowArray();
    }

    public function checkExists(string $code): bool {
        return $this->db->table("code")->getWhere(["code" => $code])->getRowArray() != null;
    }

    public function checkUsed(string $id): bool {
        $data = $this->db->table("id")->getWhere(["id" => $id])->getRowArray();
        return $data["used"];
    }

    public function checkExpired(string $id): bool {
        $data = $this->db->table("id")->getWhere(["id" => $id])->getRowArray();
        $expire = new DateTime($data["expire"]);
        $now = new DateTime();
        return $now > $expire;
    }

    public function add(string $code, int $idFoyer, string $expire): void {
        $data["id"] = $this->getMax("code", "id") + 1;
        $data = [
            "code" => $code,
            "idFoyer" => $idFoyer,
            "expire" => $expire
        ];
        $this->db->table("code")->insert($data);
    }
    
}
