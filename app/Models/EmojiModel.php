<?php

namespace App\Models;

class EmojiModel extends BaseModel {
    public function getAll(): array {
        return $this->db->table("emoji")->get()->getResultObject();
    }
}
