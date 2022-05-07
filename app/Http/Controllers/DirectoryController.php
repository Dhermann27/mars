<?php

namespace App\Http\Controllers;

use App\Http\ByyearFamily;
use App\Http\Yearattending;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DirectoryController extends Controller
{
    public function index()
    {
        $years = Yearattending::select('year_id')->where('camper_id', Auth::user()->camper->id)->get();
        $letters = ByyearFamily::select(DB::raw('UPPER(LEFT(`familyname`, 1)) AS firstletter'), 'id', 'year',
            'familyname', 'address1', 'address2', 'city', 'provincecode', 'zipcd',
            DB::raw('GROUP_CONCAT(`year`) AS years'))->groupBy('id')->where('is_address_current', 1)
            ->whereIn('year_id', $years)->orderBy('familyname')->orderBy('provincecode')->orderBy('city')
            ->with('campers')->get();
        return view('directory', ['letters' => $letters]);
    }
}
