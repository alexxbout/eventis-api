<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventCategoryTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "name" => [
        "type" => "VARCHAR",
        "constraint" => 50,
        "collation" => "utf8mb4_general_ci",
      ],
      "emoji" => [
        "type" => "VARCHAR",
        "constraint" => 30,
        "collation" => "utf8mb4_general_ci",
      ],
    ]);

    $this->forge->addKey("id", true);
    $this->forge->addForeignKey("emoji", "emoji", "code", "RESTRICT", "RESTRICT");
    $this->forge->addUniqueKey("name");
    $this->forge->createTable("event_category");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("event_category");
  }
}
