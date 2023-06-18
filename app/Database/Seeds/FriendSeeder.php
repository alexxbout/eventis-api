<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FriendSeeder extends Seeder {
  public function run() {
    // friend : idUser1	idUser2	since

    $data = [[]];

    $this->db->table("friend")->insertBatch($data);
  }
}
