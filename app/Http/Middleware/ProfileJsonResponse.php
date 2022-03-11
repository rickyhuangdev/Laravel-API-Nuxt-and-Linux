<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProfileJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $response = $next($request);
        if (!app()->bound('debugbar') || !app('debugbar')->isEnabled()) {
            return $response;
        }
        if ($response instanceof JsonResponse && $request->has('_debug')) {
//            $response->setData(array_merge($response->getData(true), [
//                '_debugbar' => app('debugbar')->getData()
//            ]));
            $response->setData(array_merge($response->getData(true), [
                '_debugbar' => Arr::only(app('debugbar')->getData(),'queries')
            ]));
        }

        return $response;
    }
}
