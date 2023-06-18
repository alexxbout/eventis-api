<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FoyerSeeder extends Seeder {
  public function run() {
    // foyer : id	siret	city zip address

    $data = [[]];

    $this->db->table("foyer")->insertBatch($data);
  }
}
