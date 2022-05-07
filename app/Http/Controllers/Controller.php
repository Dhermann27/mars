<?php

namespace App\Http\Controllers;

use App\Models\Year;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    protected $year;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->year = Year::where('is_current', '1')->first();
        View::share('year', $this->year);

    }
}
