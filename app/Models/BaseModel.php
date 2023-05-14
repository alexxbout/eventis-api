<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model {

    protected $db;
    
    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    protected function getMax(string $table, string $column): int {
        $max = $this->db->table($table)->selectMax($column)->get()->getRowArray()[$column];
        return $max === null ? -1 : $max;
    }
}