<?php

namespace App\Models;

class CodeModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("code")->get()->getResultArray();
    }

    public function getByCode(string $code): array|null {
        return $this->db->table("code")->getWhere(["code" => $code])->getRowArray();
    }

    public function getById(int $id): array|null {
        return $this->db->table("code")->getWhere(["id" => $id])->getRowArray();
    }

    public function checkExists(string $code): bool {
        return $this->getByCode($code) != null;
    }

    public function setUsed(int $id): void {
        $this->db->table("code")->update(["used" => 1], ["id" => $id]);
    }

    public function add(string $code, int $idFoyer, string $expire): int {
        $data = [
            "id" => $this->getMax("code", "id") + 1,
            "code" => $code,
            "idFoyer" => $idFoyer,
            "expire" => $expire
        ];
        $this->db->table("code")->insert($data);

        return $data["id"];
    }
    
}
