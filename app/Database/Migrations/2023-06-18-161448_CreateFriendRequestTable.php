<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFriendRequestTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "idRequester" => [
        "type" => "INT",
      ],
      "idRequested" => [
        "type" => "INT",
      ],
    ]);

    $this->forge->addKey("id", true);
    $this->forge->addForeignKey("idRequester", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idRequested", "user", "id", "RESTRICT", "RESTRICT");

    $this->forge->createTable("friend_request");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("friend_request");
  }
}
