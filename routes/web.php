<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/contact', [ContactController::class, 'contactIndex'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'contactStore'])->name('contact.store');
Route::get('/refreshcaptcha', [ContactController::class, 'refreshCaptcha'])->name('contact.refresh');

Route::get('/cost', [HomeController::class, 'campcost'])->name('cost');
Route::get('/themespeaker', [HomeController::class, 'themespeaker'])->name('themespeaker');
Route::get('/scholarship', [HomeController::class, 'scholarship'])->name('scholarship');
Route::get('/programs', [HomeController::class, 'programs'])->name('programs');
Route::get('/housing', [HomeController::class, 'housing'])->name('housing');

Route::get('/workshops', [WorkshopController::class, 'display'])->name('workshops.display');
Route::get('/excursions', [WorkshopController::class, 'excursions'])->name('workshops.excursions');

Route::get('/directory', [DirectoryController::class, 'index'])->name('directory')->middleware('auth');

//Route::group(['prefix' => 'campers', [middleware' => 'auth'], function () {
//    Route::get('', [CamperController::class, 'index'])->name('campers.index');
//    Route::get('/{id?}', [CamperController::class, 'index'])->name('campers.index')->middleware('can:is-council');
//    Route::post('/', [CamperController::class, 'store'])->name('campers.store');
//});
//
//Route::group(['prefix' => 'payment', [middleware' => 'auth'], function () {
//    Route::get('', [PaymentController::class, 'index'])->name('payment.index');
//    Route::get('/{id?}', [PaymentController::class, 'index'])->name('payment.index')->middleware('can:is-council');
//    Route::post('', [PaymentController::class, 'store'])->name('payment.store');
//    Route::post('/{id?}', [PaymentController::class, 'write'])->name('payment.store')->middleware('can:is-super');
//});

//Route::group(['prefix' => 'household', [middleware' => 'auth'], function () {
//    Route::get('', [HouseholdController::class, 'index'])->name('household.index')->middleware('can:has-paid');
//    Route::get('/{id?}', [HouseholdController::class, 'index'])->name('household.index')->middleware('can:is-council');
//    Route::post('', [HouseholdController::class, 'store'])->name('household.store')->middleware('can:has-paid');
//    Route::post('/{id?}', [HouseholdController::class, 'store'])->name('household.store')->middleware('can:is-super');
//});
//
//Route::group(['prefix' => 'roomselection', [middleware' => 'auth'], function () {
//    Route::get('/', [RoomSelectionController::class, 'index'])->name('roomselection.index')->middleware('can:has-paid');
//    Route::get('/{id?}', [RoomSelectionController::class, 'index'])->name('roomselection.index')->middleware('can:is-council');
//    Route::get('/assign/{id?}', [RoomSelectionController::class, 'read'])->name('roomselection.read')->middleware('can:is-council');
//    Route::post('/', [RoomSelectionController::class, 'store'])->name('roomselection.store')->middleware('can:has-paid');
//    Route::post('/{id?}', [RoomSelectionController::class, 'store'])->name('roomselection.store')->middleware('can:is-super');
//    Route::post('/assign/{id?}', [RoomSelectionController::class, 'write'])->name('roomselection.write')->middleware('can:is-super');
//    Route::get('/map', [RoomSelectionController::class, 'map'])->name('roomselection.map')->middleware('can:is-council');
//});
//
//Route::group(['prefix' => 'workshopchoice', [middleware' => 'auth'], function () {
//    Route::get('/', [WorkshopController::class, 'index'])->name('workshopchoice.index')->middleware('can:has-paid');
//    Route::get('/{id?}', [WorkshopController::class, 'index'])->name('workshopchoice.index')->middleware('can:is-council');
//    Route::post('/', [WorkshopController::class, 'store'])->name('workshopchoice.store')->middleware('can:has-paid');
//    Route::post('/{id?}', [WorkshopController::class, 'store'])->name('workshopchoice.store')->middleware('can:is-super');
//});
//
//Route::group(['prefix' => 'nametag', [middleware' => 'auth'], function () {
//    Route::get('/', [NametagController::class, 'index'])->name('nametag.index')->middleware('can:has-paid');
//    Route::post('/', [NametagController::class, 'store'])->name('nametag.store')->middleware('can:has-paid');
//    Route::get('/nametag/{i}/{id}', [NametagController::class, 'read')->middleware('auth', [role:admin|council');
//    Route::post('/nametag/f/{id}', [NametagController::class, 'write')->middleware('auth', [role:admin');
//});
//
//Route::group(['prefix' => 'data'], function () {
//    Route::get('loginsearch', [DataController::class, 'loginsearch');
//    Route::get('camperlist', [DataController::class, 'campers')->middleware('can:is-council');
//    Route::get('churchlist', [DataController::class, 'churches')->middleware('auth');
//    Route::get('steps', [DataController::class, 'steps')->middleware('can:has-paid');
//    Route::get('steps/{id?}', [DataController::class, 'steps')->middleware('can:is-council');
//});
Route::group(['middleware' => ['auth', 'can:is-council'], 'prefix' => 'reports'], function () {
//    Route::get('campers', [ReportController::class, 'campers'])->name('reports.campers');
//    Route::get('campers/{year}.xls', [ReportController::class, 'campersExport'])->name('reports.campers.export');
//    Route::get('chart', [ReportController::class, 'chart'])->name('reports.chart');
//    Route::get('conflicts', [ReportController::class, 'conflicts');
//    Route::get('deposits', [ReportController::class, 'deposits'])->name('reports.deposits')->middleware('can:is-council');
//    Route::post('deposits/{id}', [ReportController::class, 'depositsMark'])->name('reports.deposits.mark')->middleware('can:is-super');
//    Route::get('firsttime', [ReportController::class, 'firsttime');
//    Route::get('guarantee', [ReportController::class, 'guarantee');
//    Route::get('outstanding', [ReportController::class, 'outstanding'])->name('reports.outstanding');
//    Route::get('outstanding/{filter?}', [ReportController::class, 'outstanding');
//    Route::post('outstanding/{id}', [ReportController::class, 'outstandingMark'])->name('reports.outstanding.mark')->middleware('can:is-super');
//    Route::get('payments', [ReportController::class, 'payments');
//    Route::get('payments/{year}.xls', [ReportController::class, 'paymentsExport');
//    Route::get('payments/{year?}/name', [ReportController::class, 'payments');
//    Route::get('programs', [ReportController::class, 'programs'])->name('reports.programs');
//    Route::get('rates', [ReportController::class, 'rates');
//    Route::post('rates', [ReportController::class, 'ratesMark')->middleware('auth', [role:admin');
    Route::get('roommates', [ReportController::class, 'roommates'])->name('reports.roommates');
//    Route::get('rooms', [ReportController::class, 'rooms'])->name('reports.rooms');
//    Route::get('rooms/{year}.xls', [ReportController::class, 'roomsExport');
//    Route::get('rooms/{year?}/name', [ReportController::class, 'rooms');
//    Route::get('states', [ReportController::class, 'states');
//    Route::get('unassigned', [ReportController::class, 'unassigned');
//    Route::get('volunteers', [ReportController::class, 'volunteers');
//    Route::get('workshops', [ReportController::class, 'workshops'])->name('reports.workshops');
});

//Route::group(['middleware' => ['auth', [can:is-council'], 'prefix' => 'tools'], function () {
//    Route::get('cognoscenti', [ToolsController::class, 'cognoscenti'])->name('tools.cognoscenti');
//    Route::get('nametags', [ToolsController::class, 'nametagsList');
//    Route::post('nametags', [ToolsController::class, 'nametagsPrint');
//    Route::get('nametags/all', [ToolsController::class, 'nametags');
//    Route::get('nametags/{i}/{id}', [ToolsController::class, 'nametagsFamily');
//    Route::get('programs', [ToolsController::class, 'programIndex');
//    Route::post('programs', [ToolsController::class, 'programStore');
//    Route::get('staffpositions', [ToolsController::class, 'positionIndex'])->name('tools.staff.index');
//    Route::post('staffpositions', [ToolsController::class, 'positionStore'])->name('tools.staff.store');
//    Route::get('workshops', [ToolsController::class, 'workshopIndex');
//    Route::post('workshops', [ToolsController::class, 'workshopStore');
//});
//
//Route::group(['middleware' => ['can:is-super'], 'prefix' => 'admin'], function () {
//    Route::get('distlist', [AdminController::class, 'distlistIndex'])->name('admin.distlist.index');
//    Route::post('distlist', [AdminController::class, 'distlistExport'])->name('admin.distlist.export');
//    Route::get('master', [AdminController::class, 'masterIndex');
//    Route::post('master', [AdminController::class, 'masterStore');
//    Route::get('massassign', [AdminController::class, 'massAssignIndex');
//    Route::post('massassign/f/{id}', [AdminController::class, 'massAssignStore');
//    Route::get('roles', [AdminController::class, 'roleIndex'])->name('admin.roles.index');
//    Route::post('roles', [AdminController::class, 'roleStore'])->name('admin.roles.store');
//    Route::get('positions', [AdminController::class, 'positionIndex'])->name('admin.positions.index');
//    Route::post('positions', [AdminController::class, 'positionStore'])->name('admin.positions.store');
//});
//
Route::get('/brochure', function () {
    $year = date('Y');
    if (!is_file(public_path('MUUSA_' . $year . '_Brochure.pdf'))) $year--;
    return redirect('MUUSA_' . $year . '_Brochure.pdf');
})->name('brochure');
//Route::get('/troutlodge', function () {
//    return redirect('Trout_Lodge.pdf');
//})->name('troutlodgebrochure');

