<?php

declare(strict_types=1);

use App\Handler\EducationHandler;
use App\Handler\ExportHandler;
use App\Handler\UserHandler;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

/**
 * FastRoute route configuration
 *
 * @see https://github.com/nikic/FastRoute
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/{id:\d+}', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/', App\Handler\HomePageHandler::class, 'home');
    $app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');

    // API routes for users
    $app->route('/api/users', UserHandler::class, ['GET', 'POST', 'PUT', 'DELETE'], 'api.users');
    $app->route('/api/users/{id:\d+}', UserHandler::class, ['GET'], 'api.users.id');

    // API routes for education
    $app->route('/api/education', EducationHandler::class, ['GET', 'POST', 'PUT', 'DELETE'], 'api.education');
    $app->route('/api/education/{id:\d+}', EducationHandler::class, ['GET'], 'api.education.id');

    // Export routes
    $app->get('/api/export', ExportHandler::class, 'api.export');
};
