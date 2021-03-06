<?php

namespace App\Http\Middleware;

class LogAfterRequest
{

    public function handle($request, \Closure $next)
    {
        $url         = $request->fullUrl();
        $ip          = $request->ip();
        $log           = new \App\Models\Log();
        $log->ip       = $ip;
        $log->url      = $url;
        $log->header = json_encode($request->header());
        $log->request  = json_encode($request->all());
        $log->save();
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $url         = $request->fullUrl();
        $ip          = $request->ip();
        $log           = new \App\Models\Log();
        $log->ip       = $ip;
        $log->url      = $url;
        $log->header = json_encode($request->header());
        $log->request  = json_encode($request->all());
        $log->response = $response;
        $log->save();
    }
}
