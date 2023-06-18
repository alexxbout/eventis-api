<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserInterestSeeder extends Seeder {
  public function run() {
    // user_interest : idUser	idInterest

    $data = [[]];

    $this->db->table("user_interest")->insertBatch($data);
  }
}
