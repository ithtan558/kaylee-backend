<?php

namespace App\Http\Middleware;

class LogAfterRequest
{

    public function handle($request, \Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $url         = $request->fullUrl();
        $ip          = $request->ip();
        $log           = new \App\Models\Log();
        $log->ip       = $ip;
        $log->url      = $url;
        $log->request  = json_encode($request->all());
        $log->response = $response;
        $log->save();
    }
}
