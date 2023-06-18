<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FriendSeeder extends Seeder {
  public function run() {
    // friend : idUser1	idUser2	since

    $data = [
      [
        "idUser1" => 1,
        "idUser2" => 2,
        "since" => date("Y-m-d H:i:s")
      ],
      [
        "idUser1" => 1,
        "idUser2" => 3,
        "since" => date("Y-m-d H:i:s")
      ],
      [
        "idUser1" => 1,
        "idUser2" => 4,
        "since" => date("Y-m-d H:i:s")
      ],
      [
        "idUser1" => 2,
        "idUser2" => 3,
        "since" => date("Y-m-d H:i:s")
      ],
      [
        "idUser1" => 2,
        "idUser2" => 4,
        "since" => date("Y-m-d H:i:s")
      ],
      [
        "idUser1" => 3,
        "idUser2" => 4,
        "since" => date("Y-m-d H:i:s")
      ],
      [
        "idUser1" => 3,
        "idUser2" => 5,
        "since" => date("Y-m-d H:i:s")
      ],
      [
        "idUser1" => 4,
        "idUser2" => 5,
        "since" => date("Y-m-d H:i:s")
      ]
    ];

    $this->db->table("friend")->insertBatch($data);
  }
}
