<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FriendRequestSeeder extends Seeder {
  public function run() {
    // friend_request : id idRequester idRequested

    $data = [
      [
        "id" => 0,
        "idRequester" => 1,
        "idRequested" => 5
      ],
      [
        "id" => 1,
        "idRequester" => 5,
        "idRequested" => 2
      ]
    ];

    $this->db->table("friend_request")->insertBatch($data);
  }
}
