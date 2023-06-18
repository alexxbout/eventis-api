<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoleTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "libelle" => [
        "type" => "VARCHAR",
        "constraint" => 20,
        "collation" => "utf8mb4_general_ci",
      ],
    ]);

    $this->forge->addKey("id", true);

    $this->forge->addUniqueKey("libelle");

    $this->forge->createTable("role");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("role");
  }
}
