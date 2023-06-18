<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder {
  public function run() {
    // notification : id idUser	idAlt	idNotifType	created

    $data = [[]];

    $this->db->table("notification")->insertBatch($data);
  }
}
