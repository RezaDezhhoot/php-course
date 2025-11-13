<?php

namespace Store\App\Controllers\Admin;

use Store\App\Controllers\Controller;
use Store\Database\DB;

class DashboardController extends Controller
{
    public function show()
    {
        $users = DB::make()->table('users')->count();
        $orders = DB::make()->table('orders')->count();
        $products = DB::make()->table('products')->count();
        $views = 0;

        return render('admin.dashboard', get_defined_vars());
    }
}
