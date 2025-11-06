<?php

use Store\Database\DB;

function env($name, $default = ""): string|null
{
    if (! empty($_ENV[$name])) {
        return $_ENV[$name];
    }

    return $default;
}

function config($path = '*')
{
    if ($path !== "*") {
        $path = explode(".", $path);
        if (sizeof($path) == 1) {
            return includeFiles(glob(sprintf("config/%s.php", $path[0])))[pathinfo($path[0], PATHINFO_FILENAME)];
        } else if (sizeof($path) > 1) {
            $target = includeFiles(glob(sprintf("config/%s.php", $path[0])));
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
    extract($data);
    $name = str_replace(".", "/", $name);
    require __DIR__ . '/views/' . $name . '.php';
    exit();
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
