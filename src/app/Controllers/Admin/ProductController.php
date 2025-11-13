<?php 

namespace Store\App\Controllers\Admin;

use Store\App\Controllers\Controller;
use Store\Database\DB;

class ProductController extends Controller {
    public function list() {
        $items = DB::make()->table('products')->get();
        return render('admin.products.list' , get_defined_vars());
    }
}