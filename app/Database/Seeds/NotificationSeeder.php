<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder {
  public function run() {
    // notification : id idUser	idAlt	idNotifType	created

    $data = [
      [
        "id" => 1,
        "idUser" => 5,
        "idAlt" => 1,
        "idNotifType" => 0,
        "created" => date("Y-m-d H:i:s")
      ],
      [
        "id" => 2,
        "idUser" => 2,
        "idAlt" => 5,
        "idNotifType" => 0,
        "created" => date("Y-m-d H:i:s")
      ]
    ];

    $this->db->table("notification")->insertBatch($data);
  }
}
