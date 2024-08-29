<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Console;

if (!defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

use Cake\Codeception\Console\Installer as CodeceptionInstaller;
use Cake\Utility\Security;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Exception;

/**
 * Provides installation hooks for when this application is installed through
 * composer. Customize this class to suit your needs.
 */
class CookieSaltInstaller
{

    /**
     * Does some routine installation tasks so people don't have to.
     *
     * @param \Composer\Script\Event $event The composer event object.
     * @throws \Exception Exception raised by validator.
     * @return void
     */
    public static function postInstall(Event $event): void
    {
        $io = $event->getIO();

        $rootDir = dirname(__DIR__, 2);

        static::setSecuritySalt($rootDir, $io);
    }

    /**
     * Set the security.salt value in the application's config file.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function setSecuritySalt(string $dir, IOInterface $io): void
    {
        $newKey = hash('sha256', Security::randomBytes(64));
        static::setCookieSecuritySaltInFile($dir, $io, $newKey, 'app_local.php');
    }

    /**
     * Set the security.salt value in a given file
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @param string $newKey key to set in the file
     * @param string $file A path to a file relative to the application's root
     * @return void
     */
    public static function setCookieSecuritySaltInFile(string $dir, IOInterface $io, string $newKey, string $file): void
    {
        $config = $dir . '/config/' . $file;
        $content = file_get_contents($config);

        /** @phpstan-ignore-next-line */
        $content = str_replace('__COOKIE_SALT__', $newKey, $content, $count);

        if ($count == 0) {
            $io->write('No Security.cookieKey placeholder to replace.');

            return;
        }

        $result = file_put_contents($config, $content);
        if ($result) {
            $io->write('Updated Security.cookieKey value in config/' . $file);

            return;
        }
        $io->write('Unable to update Security.cookieKey value.');
    }
}
