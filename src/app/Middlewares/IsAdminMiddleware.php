<?php

namespace Store\App\Middlewares;

class IsAdminMiddleware
{
    public function handle($next)
    {
        $user = getLoggedInUser();
        if ($user['role'] !== "admin" ) {
            http_response_code(403);
            echo "forbbiden";
            return;
        }
        $next();
    }
}
