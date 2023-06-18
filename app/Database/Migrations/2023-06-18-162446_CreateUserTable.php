<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "lastname" => [
        "type" => "VARCHAR",
        "constraint" => 30,
        "collation" => "utf8mb4_general_ci",
      ],
      "firstname" => [
        "type" => "VARCHAR",
        "constraint" => 30,
        "collation" => "utf8mb4_general_ci",
      ],
      "login" => [
        "type" => "VARCHAR",
        "constraint" => 30,
        "collation" => "utf8mb4_general_ci",
      ],
      "emoji" => [
        "type" => "VARCHAR",
        "constraint" => 30,
        "collation" => "utf8mb4_general_ci",
        "null" => true,
      ],
      "pseudo" => [
        "type" => "VARCHAR",
        "constraint" => 25,
        "collation" => "utf8mb4_general_ci",
        "null" => true,
      ],
      "showPseudo" => [
        "type" => "TINYINT",
        "constraint" => 1,
        "default" => 0,
      ],
      "password" => [
        "type" => "VARCHAR",
        "constraint" => 100,
        "collation" => "utf8mb4_general_ci",
      ],
      "idRole" => [
        "type" => "INT",
      ],
      "idFoyer" => [
        "type" => "INT",
      ],
      "active" => [
        "type" => "TINYINT",
        "constraint" => 1,
        "default" => 1,
      ],
      "pic" => [
        "type" => "VARCHAR",
        "constraint" => 100,
        "collation" => "utf8mb4_general_ci",
        "null" => true,
      ],
      "bio" => [
        "type" => "VARCHAR",
        "constraint" => 135,
        "collation" => "utf8mb4_general_ci",
      ],
    ]);

    $this->forge->addKey("id", true);

    $this->forge->addForeignKey("idRole", "role", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idFoyer", "foyer", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("emoji", "emoji", "code", "RESTRICT", "RESTRICT");

    $this->forge->addUniqueKey("login");
    $this->forge->addUniqueKey("pseudo");

    $this->forge->createTable("user");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("user");
  }
}
