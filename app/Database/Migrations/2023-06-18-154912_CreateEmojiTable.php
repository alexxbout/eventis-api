<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmojiTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "code" => [
        "type" => "VARCHAR",
        "constraint" => 30,
        "collation" => "utf8mb4_general_ci",
      ],
    ]);

    $this->forge->addKey("code", true);
    $this->forge->addUniqueKey("code");
    $this->forge->createTable("emoji");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("emoji");
  }
}
