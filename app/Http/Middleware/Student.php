<?php

namespace App\Http\Middleware;

use Closure;

class Student
{
    public function handle($request, Closure $next)
    {
        if ($request->user()->group != 'Student' && $request->user()->group != 'Admin') {
            $notification = ['title' => 'Access Denied!', 'body' => 'Students only.'];
            return redirect()->route('user.dashboard')->with('error', $notification);
        }
        return $next($request);
    }
}
