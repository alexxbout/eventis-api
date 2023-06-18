<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateBlockedTable extends Migration {

  public function up() {
    $this->db->disableForeignKeyChecks();

    $this->forge->addField([
      "id" => [
        "type" => "INT",
      ],
      "idUser" => [
        "type" => "INT",
      ],
      "idBlocked" => [
        "type" => "INT",
      ],
      "since" => [
        "type" => "TIMESTAMP",
        "null" => false,
        "default" => new RawSql('CURRENT_TIMESTAMP'),
      ]]);

    $this->forge->addKey("id", true);
    $this->forge->addForeignKey("idUser", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->addForeignKey("idBlocked", "user", "id", "RESTRICT", "RESTRICT");
    $this->forge->createTable("blocked");

    $this->db->enableForeignKeyChecks();
  }

  public function down() {
    $this->forge->dropTable("blocked");
  }
}
