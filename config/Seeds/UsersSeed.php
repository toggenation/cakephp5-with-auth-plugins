<?php

declare(strict_types=1);

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Chronos\Chronos;
use Cake\Utility\Security;
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
                    'password' => $this->_pass('123'),
                    'created' => Chronos::now(),
                    'modified' => Chronos::now(),
                    'token' => Security::hash('aaaa', 'sha256'),
                    'token_active' => true
                ],
                [
                    'email' => 'test2@example.com',
                    'password' => $this->_pass('456'),
                    'created' => Chronos::now(),
                    'modified' => Chronos::now(),
                    'token' => Security::hash('bbbb', 'sha256'),
                    'token_active' => true,
                ]
            ];

        $table = $this->table('users');
        $table->truncate();
        $table->insert($data)->save();
    }

    protected function _pass($pass)
    {
        return (new DefaultPasswordHasher())->hash($pass);
    }
}
