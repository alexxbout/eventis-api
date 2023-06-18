<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateCodeTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "code" => [
        "type" => "VARCHAR",
        "constraint" => 6,
        "collation" => "utf8mb4_bin",
      ],
      "idFoyer" => [
        "type" => "INT",
      ],
      "expire" => [
        "type" => "TIMESTAMP",
        "null" => false,
        "default" => new RawSql('CURRENT_TIMESTAMP'),
      ],
      "used" => [
        "type" => "TINYINT",
        "constraint" => 1,
        "default" => 0,
      ],
      "createdBy" => [
        "type" => "INT",
      ],
      "idRole" => [
        "type" => "INT",
      ],
    ]);

    $this->forge->addKey("id", true);
    $this->forge->addForeignKey("idFoyer", "foyer", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("createdBy", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idRole", "role", "id", "RESTRICT", "RESTRICT");
    $this->forge->addUniqueKey("code");
    $this->forge->createTable("code");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("code");
  }
}
