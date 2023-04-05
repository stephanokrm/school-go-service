<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class DatabaseTransactionMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed|Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->needsTransaction($request)) {
            return $next($request);
        }

        DB::beginTransaction();

        try {
            $response = $next($request);
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }

        $response instanceof Response && $response->isSuccessful() ? DB::commit() : DB::rollBack();

        return $response;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function needsTransaction(Request $request): bool
    {
        return $request->isMethod('POST')
            || $request->isMethod('PUT')
            || $request->isMethod('PATCH')
            || $request->isMethod('DELETE');
    }
}
