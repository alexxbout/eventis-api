<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFoyerTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "siret" => [
        "type" => "VARCHAR",
        "constraint" => 14,
        "collation" => "utf8mb4_general_ci",
      ],
      "city" => [
        "type" => "VARCHAR",
        "constraint" => 10,
        "collation" => "utf8mb4_general_ci",
      ],
      "zip" => [
        "type" => "VARCHAR",
        "constraint" => 5,
        "collation" => "utf8mb4_general_ci",
      ],
      "address" => [
        "type" => "VARCHAR",
        "constraint" => 50,
        "collation" => "utf8mb4_general_ci",
      ],
    ]);

    $this->forge->addKey("id", true);
    $this->forge->addUniqueKey("siret");
    $this->forge->createTable("foyer");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("foyer");
  }
}
