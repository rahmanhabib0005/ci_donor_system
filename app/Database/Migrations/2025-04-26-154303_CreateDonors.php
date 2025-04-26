<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDonors extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'blood_group' => ['type' => 'VARCHAR', 'constraint' => 5],
            'district'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'thana'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_donate' => ['type' => 'DATE', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('donors');
    }

    public function down()
    {
        $this->forge->dropTable('donors');
    }
}
