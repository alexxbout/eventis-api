<?php

namespace App\Models;

class FoyerModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("foyer")->get()->getResultObject();
    }

    public function getById(int $id): object|null {
        return $this->db->table("foyer")->getWhere(["id" => $id])->getRowObject();
    }

    public function add(array $data): bool {
        $data["id"] = $this->getMax("foyer", "id") + 1;
        return $this->db->table("foyer")->insert($data);
    }
}
