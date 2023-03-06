<?php

namespace App\Models;

class FoyerModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table('foyer')->get()->getResultArray();
    }

    public function getById(int $id): array {
        return $this->db->table('foyer')->getWhere(['id' => $id])->getRowArray(); //devrait pas etre tableau
    }

    public function add(array $data): void {
        $data['id'] = $this->getMax("foyer", "id") + 1;
        $this->db->table('foyer')->insert($data);
    }
}
