<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventSeeder extends Seeder {
  public function run() {
    // event : id	idFoyer	zip	address	city canceled	reason start title idCategory	description	pic

    $data = [[]];

    $this->db->table("event")->insertBatch($data);
  }
}
