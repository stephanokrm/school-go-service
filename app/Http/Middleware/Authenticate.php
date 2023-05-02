<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * @param Request $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        $login = config('app.frontend_url') . '/login?redirect=' . urlencode($request->fullUrl());

        return $request->expectsJson()
            ? null
            : $login;
    }
}
