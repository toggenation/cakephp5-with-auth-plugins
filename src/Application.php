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
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App;

use App\Middleware\UnauthorizedHandler\CustomRedirectHandler;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\ORM\Locator\TableLocator;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
// In src/Application.php add the following imports
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Exception\ForbiddenException;
use Authorization\Exception\MissingIdentityException;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Policy\OrmResolver;
use Cake\Http\Middleware\EncryptedCookieMiddleware;
use Cake\Http\ServerRequest;
use Cake\I18n\Middleware\LocaleSelectorMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 *
 * @extends \Cake\Http\BaseApplication<\App\Application>
 */
class Application extends BaseApplication
implements
    AuthenticationServiceProviderInterface,
    AuthorizationServiceProviderInterface
{
    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI !== 'cli') {
            FactoryLocator::add(
                'Table',
                (new TableLocator())->allowFallbackClass(false)
            );
        }

        $this->addPlugin('Authorization');
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error'), $this))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance.
            // See https://github.com/CakeDC/cakephp-cached-routing
            ->add(new RoutingMiddleware($this))
            // Add middleware and set the valid locales
            ->add(new LocaleSelectorMiddleware(['en_US', 'en_AU']));
        // To accept any locale header value
        //  $middlewareQueue->add(new LocaleSelectorMiddleware(['*']));




        // Parse various types of encoded request bodies so that they are
        // available as array through $request->getData()
        // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
        $middlewareQueue->add(new BodyParserMiddleware())
            ->add(new AuthenticationMiddleware($this))
            ->add(new AuthorizationMiddleware($this,   ['unauthorizedHandler' => [
                'className' => CustomRedirectHandler::class,
                'url' => ['controller' => 'Posts', 'action' => 'unauth'],
                'queryParam' => 'redirectUrl',
                'exceptions' => [
                    MissingIdentityException::class,
                    ForbiddenException::class,
                ],
            ],]));


        $csrf = new CsrfProtectionMiddleware([
            'httponly' => true,
        ]);

        // Token check will be skipped when callback returns `true`.
        $csrf->skipCheckCallback(function (ServerRequest $request) {
            // Skip token check for API URLs.
            if (
                $request->getParam('action') === 'ajax'
                && $request->getHeaderLine('X-My-Custom-Header') === 'hijames'
            ) {
                return true;
            }
        });

        // Ensure routing middleware is added to the queue before CSRF protection middleware.
        $middlewareQueue->add($csrf);

        $cookies = new EncryptedCookieMiddleware(
            // Names of cookies to protect
            ['james'],
            Configure::read('Security.cookieKey')
        );

        $middlewareQueue->add($cookies);

        return $middlewareQueue;
    }

    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container The Container to update.
     * @return void
     * @link https://book.cakephp.org/4/en/development/dependency-injection.html#dependency-injection
     */
    public function services(ContainerInterface $container): void
    {
    }

    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $authenticationService = new AuthenticationService([
            'unauthenticatedRedirect' => Router::url('/users/login'),
            'queryParam' => 'redirect',
        ]);

        /**
         * @var \Cake\Http\ServerRequest $request
         */
        if ($request->is('ajax')) {
            $authenticationService->loadIdentifier('Authentication.Token', [
                'dataField' => 'token',
                'tokenField' => 'token',
                'resolver' => [
                    'className' => 'Authentication.Orm',
                    'userModel' => 'Users',
                    'finder' => 'token', // default: 'all'
                ],
                'hashAlgorithm' => 'sha256'
            ]);

            $authenticationService->loadAuthenticator('Authentication.Token', [
                'queryParam' => 'token',
                'header' => 'Authorization',
                'tokenPrefix' => 'Token'
            ]);
        } else {
            // Load identifiers, ensure we check email and password fields
            $authenticationService->loadIdentifier('Authentication.Password', [
                'fields' => [
                    'username' => 'email',
                    'password' => 'password',
                ],
            ]);

            // Load the authenticators, you want session first
            $authenticationService->loadAuthenticator('Authentication.Session');
            // Configure form data check to pick email and password
            $authenticationService->loadAuthenticator('Authentication.Form', [
                'fields' => [
                    'username' => 'email',
                    'password' => 'password',
                ],
                'loginUrl' => Router::url('/users/login'),
            ]);
        }

        return $authenticationService;
    }

    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        $resolver = new OrmResolver();

        return new AuthorizationService($resolver);
    }
}
