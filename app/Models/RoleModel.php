<?php

namespace App\Models;

class RoleModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table('role')->get()->getResultArray();
    }

    public function getById(int $id): array {
        return $this->db->table('role')->getWhere(['id' => $id])->getRowArray();
    }

    public function getByLibelle(String $libelle): array {
        return $this->db->table('role')->getWhere(['libelle' => $libelle])->getResultArray();
    }
}
