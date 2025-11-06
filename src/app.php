<?php

namespace Store;

use Dotenv\Dotenv;
use Store\Database\DB;
use FastRoute;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;


final class App
{
    public static function run()
    {
        session_start();
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        static::routes();
    }

    private static function routes()
    {
        
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            require_once __DIR__ . '/routes/web.php';
        });
        
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar)
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                http_response_code(404);
                echo "404 Not Found<br>URI: " . htmlspecialchars($uri);
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                echo '405 Method Not Allowed';
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $middlwares = $handler['middlwares'] ?? [];
                if (isset($handler['handler'])) {
                    $handler = $handler['handler'];
                }

                $next = function () use ($handler, $vars) {
                    [$controller, $method] = $handler;
                    $instance = new $controller;
                    // method 1:
                    call_user_func_array([$instance, $method], $vars);

                    // method 2:
                    // $instance->{$method}($vars);
                };

                if (sizeof($middlwares) > 0) {
                    foreach (array_reverse($middlwares) as $key => $m) {
                        $next = function () use ($m, $next) {
                            $instance = new $m;
                            // method 1:
                            call_user_func_array([$instance, 'handle'], [$next]);

                            // method 2:
                            // $instance->handle($next);
                        };
                    }
                }
                $next();
                break;
        }
    }
}
