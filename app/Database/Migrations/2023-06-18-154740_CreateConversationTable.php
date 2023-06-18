<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateConversationTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "idUser1" => [
        "type" => "INT",
      ],
      "idUser2" => [
        "type" => "INT",
      ],
      "hidden" => [
        "type" => "TINYINT",
        "constraint" => 1,
        "default" => 0,
      ],
      "lastMessage" => [
        "type" => "VARCHAR",
        "constraint" => 50,
        "collation" => "utf8mb4_general_ci",
      ],
      "sentAt" => [
        "type" => "DATETIME",
        "null" => false,
        "default" => new RawSql('CURRENT_TIMESTAMP'),
      ],
    ]);

    $this->forge->addKey("id", true);
    $this->forge->addForeignKey("idUser1", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idUser2", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->createTable("conversation");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("conversation");
  }
}
