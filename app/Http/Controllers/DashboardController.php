<?php

namespace App\Http\Controllers;

use App\Enums\Chargetypename;
use App\Models\ChartdataDays;
use App\Models\ThisyearCamper;
use App\Models\ThisyearCharge;
use App\Models\ThisyearFamily;
use App\Models\Yearattending;

class DashboardController extends Controller
{
    public function admin()
    {
        $homeless = ThisyearFamily::where('is_address_current', '0')->count();
        $campers = ThisyearCamper::orderBy('familyname')->orderBy('age', 'desc')->get();
        $average = ChartdataDays::selectRaw('onlyday, AVG(count) AS average')
            ->whereRaw('REPLACE(onlyday, "-", "") >= ' . date('md'))
            ->groupBy('onlyday')->orderBy('onlyday')->firstOrFail();
        $last7 = Yearattending::where('year_id', $this->year->id)->whereRaw('updated_at >= DATE(NOW()-INTERVAL 7 DAY)')
            ->orderBy('updated_at', 'desc')->with('camper')->get();
        $deposits = ThisyearCharge::where('is_deposited', '1')
            ->join('chargetypes', 'chargetypes.id', 'thisyear_charges.chargetype_id')->count();
        $charges = ThisyearCharge::selectRaw('CONCAT(chargetypename,"s") AS name, FORMAT(SUM(amount), 2) AS total')->where('amount', '>', '0')
            ->groupBy('chargetype_id')->get();
        $charges->prepend(['name' => 'Guarantee Amount', 'total' => 230000-$charges->sum('amount')]);
        return view('admin.admin', ['campers' => $campers, 'last7' => $last7, 'deposits' => $deposits,
            'homeless' => $homeless, 'average' => $average, 'charges' => $charges]);

    }

    public function index()
    {
        return view('dashboard', ['stepdata' => $this->getStepData()]);
    }

}
