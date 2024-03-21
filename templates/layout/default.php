<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

use Cake\Utility\Inflector;
use Cake\Utility\Text;

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>

<body>
    <nav class="top-nav">
        <div class="top-nav-title">
            <a href="<?= $this->Url->build('/') ?>"><span>Cake</span>PHP</a>
        </div>
        <div class="top-nav-links">
            <?= $this->getRequest()->getAttribute('identity') ?
                "Logged in as: " . $this->getRequest()->getAttribute('identity')
                    ->getOriginalData()['email'] :
                ''; ?>
            <?= $this->getRequest()->getAttribute('identity') ?
                $this->Html->link(
                    'Logout',
                    ['controller' => 'Users', 'action' => 'logout']
                ) :
                $this->Html->link(
                    'Login',
                    ['controller' => 'Users', 'action' => 'login']
                ); ?>
            <?php foreach (['posts', 'users'] as $controller) : ?>
                <?= $this->Html->link(
                    Inflector::humanize($controller),
                    ['controller' => $controller]
                ); ?>
            <?php endforeach; ?>

        </div>
    </nav>
    <main class="main">
        <div class="container">
            <?= $this->Html->link('Ajax', ['controller' => 'Posts', 'action' => 'ajax']); ?>

            <?= $this->Html->link('Test Redirect', ['controller' => 'Posts', 'action' => 'testRedirect']); ?>
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <footer>
    </footer>
</body>

</html>