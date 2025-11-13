<?php

namespace Store\App\Controllers\Site;

use Store\App\Controllers\Controller;
use Store\Database\DB;

class AuthController extends Controller
{
    public function login()
    {
        return render('site.login');
    }

    public function postLogin()
    {
        $messages = [];
        if (empty($_POST["phone"] = trim($_POST["phone"]))) {
            $messages["phone"] = "شماره همراه اچباری می باشد";
        }
        if (! is_numeric($_POST['phone'])) {
            $messages["phone"] = "شماره همراه نامعبر ";
        }
        if (strlen($_POST["phone"]) !== 12 && strlen($_POST["phone"]) !== 11) {
            $messages['phone'] = sprintf("حداکثر طول شماره %d و حداقل %d می باشد", 12, 11);
        }

        if (! preg_match("/^(09|\+989|989)((0|1|2|3|4|9)[0-9])\d{7}$/", $_POST["phone"])) {
            $messages['country'] = "شماره وارد شده معتبر نمی باشد";
        }
        if (empty($_POST["password"] = trim($_POST["password"]))) {
            $messages["password"] = "رمز عبور اجباری می باشد";
        }
        if (sizeof($messages) > 0) {
            foreach ($messages as $name => $message) {
                flash($name, $message);
            }
            header("Location:/login");
        }
        $user = DB::make()->table("users")->where("phone", "=", $_POST['phone'])->get();
        if (sizeof($user) === 1) {
            $user = $user[0];
            if ($user['password'] === md5($_POST['password'])) {
                $_SESSION['user'] = $user['id'];
                if ($user['role'] === "admin") {
                    // redirect to admin panel
                    header("Location:/admin");
                } else {
                    // redirect to user panel
                }
                return;
            }
        }
        flash('phone', "شماره همراه یا رمز عبور اشتباه است");
        header("Location:/login");
    }
}
