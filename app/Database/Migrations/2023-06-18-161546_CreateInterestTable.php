<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterestTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "name" => [
        "type" => "VARCHAR",
        "constraint" => 20,
        "collation" => "utf8mb4_general_ci",
      ],
      "emoji" => [
        "type" => "VARCHAR",
        "constraint" => 30,
        "collation" => "utf8mb4_general_ci",
      ],
      "color" => [
        "type" => "VARCHAR",
        "constraint" => 7,
        "collation" => "utf8mb4_general_ci",
      ],
    ]);

    $this->forge->addKey("id", true);

    $this->forge->addUniqueKey("name");
    $this->forge->addForeignKey("emoji", "emoji", "code", "RESTRICT", "RESTRICT");

    $this->forge->createTable("interest");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("interest");
  }
}
