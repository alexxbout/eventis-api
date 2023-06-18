<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CodeSeeder extends Seeder {
  public function run() {
    // code : id code	idFoyer	expire used	createdBy	idRole

    $data = [[]];

    $this->db->table("code")->insertBatch($data);
  }
}
