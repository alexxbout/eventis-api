<?php

namespace App\Models;

class FoyerModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("foyer")->get()->getResultArray();
    }

    public function getById(int $id): object|null {
        return $this->db->table("foyer")->getWhere(["id" => $id])->getRowObject();
    }

    public function getByZip(int $zip): array {
        return $this->db->table("foyer")->getWhere(["zip" => $zip])->getResultObject();
    }

    public function add(array $data): void {
        $data["id"] = $this->getMax("foyer", "id") + 1;
        $this->db->table("foyer")->insert($data);
    }
}
