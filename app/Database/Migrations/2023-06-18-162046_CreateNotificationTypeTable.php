<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationTypeTable extends Migration {
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
      "active" => [
        "type" => "TINYINT",
        "constraint" => 1,
        "default" => 1,
      ],
    ]);

    $this->forge->addKey("id", true);

    $this->forge->addUniqueKey("libelle");

    $this->forge->createTable("notification_type");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("notification_type");
  }
}
