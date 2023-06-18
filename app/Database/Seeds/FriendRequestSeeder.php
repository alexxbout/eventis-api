<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FriendRequestSeeder extends Seeder {
  public function run() {
    // friend_request : id idRequester idRequested

    $data = [[]];

    $this->db->table("friend_request")->insertBatch($data);
  }
}
