<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegistrationSeeder extends Seeder {
  public function run() {
    // registration : id	idCode	idUser	at

    $data = [[]];

    $this->db->table("registration")->insertBatch($data);
  }
}
