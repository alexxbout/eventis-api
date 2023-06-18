<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder {
  public function run() {
    // role : id libelle

    $data = [
      [
        "id" => 0,
        "libelle" => "developer"
      ],
      [
        "id" => 1,
        "libelle" => "educator"
      ],
      [
        "id" => 2,
        "libelle" => "user"
      ],
      [
        "id" => 3,
        "libelle" => "admin"
      ]
    ];

    $this->db->table("role")->insertBatch($data);
  }
}
