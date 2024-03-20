<?php

declare(strict_types=1);

use Cake\Chronos\Chronos;
use Migrations\AbstractSeed;

/**
 * Posts seed.
 */
class PostsSeed extends AbstractSeed
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

        $data = [
            [
                'created' => Chronos::now(),
                'modified' => Chronos::now(),
                'title' => 'My title', 'body' => 'My Body', 'user_id' => 1
            ],
            [
                'created' => Chronos::now(),
                'modified' => Chronos::now(),
                'title' => 'My title 2', 'body' => 'My Body 2', 'user_id' => 2
            ]
        ];

        $table = $this->table('posts');
        $table->truncate();
        $table->insert($data)->save();
    }
}
