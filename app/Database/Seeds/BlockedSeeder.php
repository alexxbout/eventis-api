<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BlockedSeeder extends Seeder {
  public function run() {
    // blocked : id	idUser idBlocked since

    $data = [
      [
        "id" => 0,
        "idUser" => 1,
        "idBlocked" => 5,
        "since" => "2023-06-01 00:00:00"
      ]
    ];

    $this->db->table("blocked")->insertBatch($data);
  }
}
