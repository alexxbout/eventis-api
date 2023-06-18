<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegistrationSeeder extends Seeder {
  public function run() {
    // registration : id	idCode	idUser	at

    $data = [
      [
        "id" => 1,
        "idCode" => 1,
        "idUser" => 1,
        "at" => "2023-06-01 00:00:00"
      ],
      [
        "id" => 2,
        "idCode" => 2,
        "idUser" => 2,
        "at" => "2023-06-01 00:00:00"
      ],
      [
        "id" => 3,
        "idCode" => 3,
        "idUser" => 3,
        "at" => "2023-06-01 00:00:00"
      ],
      [
        "id" => 4,
        "idCode" => 4,
        "idUser" => 4,
        "at" => "2023-06-01 00:00:00"
      ],
      [
        "id" => 5,
        "idCode" => 5,
        "idUser" => 5,
        "at" => "2023-06-01 00:00:00"
      ],
    ];

    $this->db->table("registration")->insertBatch($data);
  }
}
