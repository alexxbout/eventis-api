<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationTypeSeeder extends Seeder {
  public function run() {
    // notification_type : id	libelle	active

    $data = [
      [
        "id" => 0,
        "libelle" => "friend_request",
        "active" => 1
      ],
      [
        "id" => 1,
        "libelle" => "new_event",
        "active" => 1
      ]
    ];

    $this->db->table("notification_type")->insertBatch($data);
  }
}
