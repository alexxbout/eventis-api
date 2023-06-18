<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ParticipantSeeder extends Seeder {
  public function run() {
    // participant : id	idEvent	idUser

    $data = [
      [
        "id" => 1,
        "idEvent" => 1,
        "idUser" => 1
      ],
      [
        "id" => 2,
        "idEvent" => 1,
        "idUser" => 2
      ],
      [
        "id" => 3,
        "idEvent" => 1,
        "idUser" => 3
      ],
      [
        "id" => 4,
        "idEvent" => 1,
        "idUser" => 4
      ],
      [
        "id" => 5,
        "idEvent" => 1,
        "idUser" => 5
      ],
      [
        "id" => 6,
        "idEvent" => 2,
        "idUser" => 1
      ],
      [
        "id" => 7,
        "idEvent" => 2,
        "idUser" => 2
      ],
      [
        "id" => 8,
        "idEvent" => 2,
        "idUser" => 3
      ],
      [
        "id" => 9,
        "idEvent" => 2,
        "idUser" => 4
      ],
      [
        "id" => 10,
        "idEvent" => 2,
        "idUser" => 5
      ],
      [
        "id" => 11,
        "idEvent" => 3,
        "idUser" => 1
      ],
      [
        "id" => 12,
        "idEvent" => 3,
        "idUser" => 2
      ],
      [
        "id" => 13,
        "idEvent" => 3,
        "idUser" => 3
      ],
      [
        "id" => 14,
        "idEvent" => 3,
        "idUser" => 4
      ],
      [
        "id" => 15,
        "idEvent" => 3,
        "idUser" => 5
      ],
      [
        "id" => 16,
        "idEvent" => 4,
        "idUser" => 1
      ],
      [
        "id" => 17,
        "idEvent" => 4,
        "idUser" => 2
      ],
      [
        "id" => 18,
        "idEvent" => 4,
        "idUser" => 3
      ],
      [
        "id" => 19,
        "idEvent" => 4,
        "idUser" => 4
      ],
      [
        "id" => 20,
        "idEvent" => 4,
        "idUser" => 5
      ],
      [
        "id" => 21,
        "idEvent" => 5,
        "idUser" => 1
      ],
      [
        "id" => 22,
        "idEvent" => 5,
        "idUser" => 2
      ],
      [
        "id" => 23,
        "idEvent" => 5,
        "idUser" => 3
      ],
      [
        "id" => 24,
        "idEvent" => 5,
        "idUser" => 4
      ]
    ];

    $this->db->table("participant")->insertBatch($data);
  }
}
