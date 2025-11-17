<?php

namespace Store\App\Controllers\Admin;

use Store\App\Controllers\Controller;
use Store\Database\DB;

class ProductController extends Controller
{
    public function list()
    {
        $items = DB::make()->table('products')->get();
        return render('admin.products.list', get_defined_vars());
    }

    public function create()
    {
        return render('admin.products.create');
    }

    public function store()
    {
        $uploadDir = "uploads";
        $errs = [];
        $validFiles = [];
        $validFormats = [
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
        ];
        $maxFileSize = 1024 * 1024 * 2;
        // 1) Type validation
        // 1) Size validation
        if (isset($_FILES['images']) && sizeof($_FILES['images']) > 0) {
            $images = $_FILES['images'];

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
        if (sizeof($errs) > 0) {
            foreach ($errs as $name => $message) {
                flash($name, $message);
            }
            foreach ($_POST as $k => $v) {
                old($k, $v);
            }
            header("Location:/admin/products/create");
        }
        $path = [];
        foreach ($validFiles as $name => $temp) {
            $to = sprintf("/%s/%s", $uploadDir, $name);
            move_uploaded_file($temp, sprintf(__DIR__ . "/../../..%s", $to));
            $path[] = $to;
        }

        $data = [
            'title' => $_POST['title'],
            'body' => $_POST['body'],
            'price' => $_POST['price'],
            'quantity' => $_POST['quantity'],
            'status' => $_POST['status'],
            'images' => json_encode($path)
        ];
        $res = DB::make()->table('products')->create($data);
        header("Location:/admin/products");
    }
}
