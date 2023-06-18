<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BlockedSeeder extends Seeder {
  public function run() {
    // blocked : id	idUser idBlocked since

    $data = [[]];

    $this->db->table("blocked")->insertBatch($data);
  }
}
