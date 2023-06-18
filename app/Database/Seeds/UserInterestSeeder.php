<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserInterestSeeder extends Seeder {
  public function run() {
    // user_interest : idUser	idInterest

    $data = [
      [
        "idUser" => 1,
        "idInterest" => 1
      ],
      [
        "idUser" => 1,
        "idInterest" => 2
      ],
      [
        "idUser" => 1,
        "idInterest" => 3
      ],
      [
        "idUser" => 2,
        "idInterest" => 1
      ],
      [
        "idUser" => 2,
        "idInterest" => 2
      ],
      [
        "idUser" => 2,
        "idInterest" => 3
      ],
      [
        "idUser" => 3,
        "idInterest" => 1
      ],
      [
        "idUser" => 3,
        "idInterest" => 2
      ],
      [
        "idUser" => 3,
        "idInterest" => 3
      ],
      [
        "idUser" => 4,
        "idInterest" => 1
      ],
      [
        "idUser" => 4,
        "idInterest" => 2
      ],
      [
        "idUser" => 4,
        "idInterest" => 3
      ],
      [
        "idUser" => 5,
        "idInterest" => 1
      ],
      [
        "idUser" => 5,
        "idInterest" => 2
      ],
      [
        "idUser" => 5,
        "idInterest" => 3
      ]
    ];

    $this->db->table("user_interest")->insertBatch($data);
  }
}
