<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateNotificationTable extends Migration {
  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "idUser" => [
        "type" => "INT",
      ],
      "idAlt" => [
        "type" => "INT",
      ],
      "idNotifType" => [
        "type" => "INT",
      ],
      "created" => [
        "type" => "TIMESTAMP",
        "default" => new RawSql("CURRENT_TIMESTAMP"),
        "null" => false,
      ],
    ]);

    $this->forge->addKey("id", true);

    $this->forge->addForeignKey("idUser", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idNotifType", "notification_type", "id", "RESTRICT", "RESTRICT");

    $this->forge->createTable("notification");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("notification");
  }
}
