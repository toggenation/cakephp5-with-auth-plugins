<?php

declare(strict_types=1);

use Cake\Chronos\Chronos;
use Migrations\AbstractSeed;

/**
 * Users seed.
 */
class UsersSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data =
            [
                [
                    'email' => 'test@example.com',
                    'password' => '123',
                    'created' => Chronos::now(),
                    'modified' => Chronos::now(),
                ],
                [
                    'email' => 'test2@example.com',
                    'password' => '456',
                    'created' => Chronos::now(),
                    'modified' => Chronos::now(),
                ]
            ];

        $table = $this->table('users');
        $table->truncate();
        $table->insert($data)->save();
    }
}
