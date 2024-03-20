<?php

declare(strict_types=1);

use Cake\Chronos\Chronos;
use Migrations\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('email', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('password', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => 'NOW()',
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => 'NOW()',
            'null' => false,
        ]);
        $table->create();
    }
}
