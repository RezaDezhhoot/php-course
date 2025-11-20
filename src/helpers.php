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
    if (is_array($route)) {
        return in_array(getUri(), $route);
    } else {
        return $route === getUri();
    }
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


function prepareFiles($files): array
{
    $uploadDir = "uploads";
    // 1) Type validation
    // 1) Size validation
    $errs = [];
    $path = [];
    $validFiles = [];
    $validFormats = [
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
    ];
    $maxFileSize = 1024 * 1024 * 2;
    if (isset($files['name']) && sizeof(array_filter($files['name'])) > 0) {
        $images = $files;
        for ($i = 0; $i < sizeof($images['size']); $i++) {
            if ($images['size'][$i] > $maxFileSize) {
                $errs['images'] = "حجم فایل ارسال شده بیشتر از حد مجاز می باشد";
                continue;
            }
        }
        for ($i = 0; $i < sizeof($images['error']); $i++) {
            if ($images['error'][$i] > $maxFileSize) {
                $errs['images'] = "فایل نامعتبر ارسال شده است";
                continue;
            }
        }
        for ($i = 0; $i < sizeof($images['name']); $i++) {
            // Type validation method1:
            // if (! in_array($type, $validFormats)) {
            //     $err['images'] = "فایل نامعتبر ارسال شده است";
            //     continue;
            // }

            // Type validation method2:
            $ext = pathinfo($images['name'][$i], PATHINFO_EXTENSION);
            if (! in_array($ext, array_keys($validFormats))) {
                $errs['images'] = "فایل نامعتبر ارسال شده است";
                continue;
            }
            $validFiles[uniqid() . '.' . $ext] = $images['tmp_name'][$i];
        }
    }
    if (sizeof($errs) === 0) {
        foreach ($validFiles as $name => $temp) {
            $to = sprintf("/%s/%s", $uploadDir, $name);
            move_uploaded_file($temp, sprintf(__DIR__ . "/public/%s", $to));
            $path[] = $to;
        }
    }

    return [$path, $errs];
}
