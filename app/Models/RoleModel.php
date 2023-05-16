<?php

namespace App\Models;

class RoleModel extends BaseModel {

    public function getAll(): array {
        return $this->db->table("role")->get()->getResultObject();
    }
}
