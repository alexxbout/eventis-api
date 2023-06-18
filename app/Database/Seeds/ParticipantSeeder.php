<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ParticipantSeeder extends Seeder {
  public function run() {
    // participant : id	idEvent	idUser

    $data = [[]];

    $this->db->table("participant")->insertBatch($data);
  }
}
