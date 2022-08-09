<?php

namespace App\Http\Middleware;

use App\Models\Year;
use Closure;
use Illuminate\Http\Request;

class BrochureEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $year = Year::where('is_current', '1')->firstOrFail();
        if($year->is_brochure != 1) {
            $request->session()->flash('warning', 'This function will be enabled when the brochure is released.');
            return redirect('home');
        }
        return $next($request);
    }
}
