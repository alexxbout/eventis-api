<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFriendTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "idUser1" => [
        "type" => "INT",
      ],
      "idUser2" => [
        "type" => "INT",
      ],
      "since" => [
        "type" => "DATETIME",
        "null" => false,
      ],
    ]);

    $this->forge->addForeignKey("idUser1", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idUser2", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addKey(["idUser1", "idUser2"], true);

    $this->forge->createTable("friend");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("friend");
  }
}
