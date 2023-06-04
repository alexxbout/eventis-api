<?php

namespace App\Models;

class FoyerModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("foyer")->select(["id", "city", "zip", "address"])->get()->getResultObject();
    }

    public function getById(int $id): object|null {
        return $this->db->table("foyer")->getWhere(["id" => $id])->getRowObject();
    }

    public function getByZip(int $zip): array {
        return $this->db->table("foyer")->select(["id", "city", "zip", "address"])->getWhere(["zip" => $zip])->getResultObject();
    }

    public function add(object $data): int {
        $data->id = $this->getMax("foyer", "id") + 1;
        $this->db->table("foyer")->insert($data);

        if ($this->isLastQuerySuccessfull()) {
            return $data->id;
        } else {
            return -1;
        }
    }
}
