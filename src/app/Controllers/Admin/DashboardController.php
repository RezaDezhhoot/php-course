<?php

namespace Store\App\Controllers\Admin;

use Store\App\Controllers\Controller;

class DashboardController extends Controller
{
    public function show()
    {
        $users = 1;
        $orders = 1;
        $products = 0;
        $views = 0;

        return render('admin.dashboard', get_defined_vars());
    }
}
