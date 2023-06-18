<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MessageSeeder extends Seeder {
  public function run() {
    // message : id	idConversation	idSender	idReceiver	content	sentAt	unread

    $data = [[]];

    $this->db->table("message")->insertBatch($data);
  }
}
