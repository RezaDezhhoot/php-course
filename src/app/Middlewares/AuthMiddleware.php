<?php

namespace Store\App\Middlewares;

class AuthMiddleware
{
    public function handle($next)
    {
        if (! getLoggedInUser()) {
            http_response_code(401);
            header("Location:/login");
            return;
        }
        $next();
    }
}
