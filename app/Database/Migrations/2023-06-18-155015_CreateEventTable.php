<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "idFoyer" => [
        "type" => "INT",
      ],
      "zip" => [
        "type" => "VARCHAR",
        "constraint" => 2,
        "collation" => "utf8mb4_general_ci",
      ],
      "address" => [
        "type" => "VARCHAR",
        "constraint" => 150,
        "collation" => "utf8mb4_general_ci",
      ],
      "city" => [
        "type" => "VARCHAR",
        "constraint" => 50,
        "collation" => "utf8mb4_general_ci",
      ],
      "canceled" => [
        "type" => "TINYINT",
        "constraint" => 1,
        "default" => 0,
      ],
      "reason" => [
        "type" => "VARCHAR",
        "constraint" => 50,
        "collation" => "utf8mb4_general_ci",
        "null" => true,
      ],
      "start" => [
        "type" => "DATE",
      ],
      "title" => [
        "type" => "VARCHAR",
        "constraint" => 20,
        "collation" => "utf8mb4_general_ci",
      ],
      "idCategorie" => [
        "type" => "INT",
      ],
      "description" => [
        "type" => "TEXT",
        "collation" => "utf8mb4_general_ci",
      ],
      "pic" => [
        "type" => "VARCHAR",
        "constraint" => 50,
        "collation" => "utf8mb4_general_ci",
        "null" => true,
      ],
    ]);

    $this->forge->addKey("id", true);
    $this->forge->addForeignKey("idFoyer", "foyer", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idCategorie", "categorie", "id", "RESTRICT", "RESTRICT");
    $this->forge->createTable("event");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("event");
  }
}
