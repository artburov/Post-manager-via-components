<?php


namespace Models;

use Aura\SqlQuery\QueryFactory;
use Delight\Auth\Auth;
use FastRoute;
use DI\ContainerBuilder;
use League\Plates\Engine;
use PDO;

class Router
{
    public static function getRouter()
    {
        $dispatcher = FastRoute\simpleDispatcher( function ( FastRoute\RouteCollector $r ) {
            $r -> addRoute( 'GET', '/', [ 'Controllers\Homepage', 'index' ] );

            $r -> addRoute( 'GET', '/login', [ 'Controllers\Login', 'index' ] );
            $r -> addRoute( 'POST', '/login', [ 'Controllers\Login', 'login' ] );

            $r -> addRoute( 'GET', '/registration', [ 'Controllers\Register', 'index' ] );
            $r -> addRoute( 'POST', '/registration', [ 'Controllers\Register', 'registration' ] );
            $r -> addRoute( 'GET', '/verification_email', [ 'Controllers\Register', 'email_validation' ] );

            $r -> addRoute( 'POST', '/add_post', [ 'Controllers\Comments', 'addPost' ] );

            $r -> addRoute( 'GET', '/profile', [ 'Controllers\Profile', 'index' ] );
            $r -> addRoute( 'POST', '/profile', [ 'Controllers\Profile', 'profile_change' ] );

            $r -> addRoute( 'GET', '/profile_change_email', [ 'Controllers\Profile', 'change_email' ] );
            $r -> addRoute( 'POST', '/profile_change_password', [ 'Controllers\Profile', 'change_password' ] );

            $r -> addRoute( 'GET', '/admin', [ 'Controllers\Admin', 'index' ] );
            $r -> addRoute( 'POST', '/admin_deny', [ 'Controllers\Admin', 'post_deny' ] );
            $r -> addRoute( 'POST', '/admin_allow', [ 'Controllers\Admin', 'post_allow' ] );
            $r -> addRoute( 'POST', '/admin_delete', [ 'Controllers\Admin', 'post_delete' ] );
            $r -> addRoute( 'GET', '/faker', [ 'Controllers\Admin', 'faker' ] );

            $r -> addRoute( 'GET', '/logout', [ 'Controllers\Login', 'logout' ] );
        } );

// Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos( $uri, '?' )) {
            $uri = substr( $uri, 0, $pos );
        }
        $uri = rawurldecode( $uri );

        $routeInfo = $dispatcher -> dispatch( $httpMethod, $uri );

        switch ( $routeInfo[0] ) {
            case FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                echo '404';
                break;

            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                echo 'Method not allowed';
                break;

            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                /*Doesn't need when using DI-php
                $class = $handler[0];
                $method = $handler[1];
                $controller = new $class;*/

                //DI-php starts here
                $containerBuilder = new ContainerBuilder();

                $containerBuilder -> addDefinitions( [
                    Engine::class       => function () {
                        return new Engine( '../views' );
                    },
                    PDO::class          => function () {
                        return new PDO(
                            "mysql:host=localhost; dbname=comments; charset=utf8",
                            "root",
                            "" );
                    },
                    QueryFactory::class => function () {
                        return new QueryFactory( 'mysql' );
                    },
                    Auth::class         => function ( $callback ) {
                        return new Auth( $callback -> get( 'PDO' ), null, null, false );
                    }
                ] );

                $container = $containerBuilder -> build();
                $container -> call( $routeInfo[1], $routeInfo[2] );

                /* Also doesn't need when using DI-php
                call_user_func( [ $controller, $method ], $vars ); */
                break;
        }
    }
}