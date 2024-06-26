<?php
declare(strict_types=1);

namespace App\Middleware\UnauthorizedHandler;

use Authorization\Exception\Exception;
use Authorization\Middleware\UnauthorizedHandler\CakeRedirectHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CustomRedirectHandler extends CakeRedirectHandler
{
    public function handle(Exception $exception, ServerRequestInterface $request, array $options = []): ResponseInterface
    {
        /**
         * @var \Cake\Http\ServerRequest $request
         */
        $response = parent::handle($exception, $request, $options);

        $request->getFlash()->set('You are not authorized to access the location: ', [
            'element' => 'denied',
            'params' => ['redirected_from' => $request->getRequestTarget()],
        ]);

        return $response;
    }
}
