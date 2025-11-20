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
        [$path, $errs] = prepareFiles($_FILES['images']);
        if (sizeof($errs) > 0) {
            foreach ($errs as $name => $message) {
                flash($name, $message);
            }
            foreach ($_POST as $k => $v) {
                old($k, $v);
            }
            header("Location:/admin/products/create");
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

    public function destroy($id)
    {
        $res = DB::make()->table('products')->where("id", '=', $id)->delete();
        http_response_code(200);
        echo $res;
    }

    public function edit($id)
    {
        $product = DB::make()->table('products')
            ->where('id', '=', $id)
            ->first();
        if (! $product || sizeof($product) === 0) {
            header("Location:/admin/products");
            return;
        }
        return render('admin.products.edit', get_defined_vars());
    }

    public function update($id)
    {
        $product = DB::make()->table('products')
            ->where('id', '=', $id)
            ->first();
        if (! $product || sizeof($product) === 0) {
            header("Location:/admin/products");
            return;
        }
        [$path, $errs] = prepareFiles($_FILES['images']);
        if (sizeof($errs) > 0) {
            foreach ($errs as $name => $message) {
                flash($name, $message);
            }
            foreach ($_POST as $k => $v) {
                old($k, $v);
            }
            header("Location:/admin/products/edit/" . $id);
        }
        $path = [];
        $data = [
            'title' => $_POST['title'],
            'body' => $_POST['body'],
            'price' => $_POST['price'],
            'quantity' => $_POST['quantity'],
            'status' => $_POST['status'],
            'images' => sizeof($path) > 0 ? json_encode($path) : $product['images']
        ];
        DB::make()->table('products')->where('id', '=', $id)->update($data);
        header("Location:/admin/products/edit/" . $id);
    }
}
