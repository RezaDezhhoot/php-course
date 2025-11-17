<?php

use Store\Database\DB;

function env($name, $default = ""): string|null
{
    if (! empty($_ENV[$name])) {
        return $_ENV[$name];
    }

    return $default;
}

function isRoute($route)
{
    return $route === getUri();
}

function getUri()
{
    $uri = $_SERVER['REQUEST_URI'];
    $p = "/^(\?)+[\w]=[\w]$/i";
    $uri = preg_replace($p, "", $uri);

    return $uri;
}

function config($path = '*')
{
    if ($path !== "*") {
        $path = explode(".", $path);
        if (sizeof($path) == 1) {
            return includeFiles(glob(sprintf(__DIR__ . "/config/%s.php", $path[0])))[pathinfo($path[0], PATHINFO_FILENAME)];
        } else if (sizeof($path) > 1) {
            $target = includeFiles(glob(sprintf(__DIR__ . "/config/%s.php", $path[0])));
            foreach ($path as  $value) {
                $target = $target[$value];
            }
            return $target;
        }
    }
    return includeFiles(glob('config/*'));
}

function includeFiles(array $files)
{
    $data = [];
    foreach ($files as $file) {
        $data[pathinfo($file, PATHINFO_FILENAME)] = include($file);
    }

    return $data;
}


function render($name, $data = [])
{
    renderFile($name, $data);
    exit();
}

function renderFile($name, $data = [])
{
    extract($data);
    $name = str_replace(".", "/", $name);
    require __DIR__ . '/views/' . $name . '.php';
}


function getLoggedInUser()
{
    if (! empty($_SESSION['user'])) {
        $user = DB::make()
            ->table("users")
            ->where('id', '=', $_SESSION['user'])
            ->get();

        if (sizeof($user) === 1) {
            return $user[0];
        }
    }

    return null;
}

function flash($key, $msg, $ttl = 15)
{
    setcookie($key, $msg, time() + $ttl, '/');
}

function old($key, $msg)
{
    flash($key, $msg, 60);
}
