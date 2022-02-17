<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

//Route::get('/contact', 'ContactController@contactIndex')->name('contact.index');
//Route::post('/contact', 'ContactController@contactStore')->name('contact.store');
//Route::get('/refreshcaptcha', 'ContactController@refreshCaptcha')->name('contact.refresh');
//
//Route::get('/cost', 'HomeController@campcost')->name('cost');
//Route::get('/themespeaker', 'HomeController@themespeaker')->name('themespeaker');
//Route::get('/scholarship', 'HomeController@scholarship')->name('scholarship');
//Route::get('/programs', 'HomeController@programs')->name('programs');
//Route::get('/housing', 'HomeController@housing')->name('housing');
//
//Route::get('/workshops', 'WorkshopController@display')->name('workshops.display');
//Route::get('/excursions', 'WorkshopController@excursions')->name('workshops.excursions');
//
//Route::get('/directory', 'DirectoryController@index')->name('directory')->middleware('auth');
//
//Route::group(['prefix' => 'campers', 'middleware' => 'auth'], function () {
//    Route::get('', 'CamperController@index')->name('campers.index');
//    Route::get('/{id?}', 'CamperController@index')->name('campers.index')->middleware('can:is-council');
//    Route::post('/', 'CamperController@store')->name('campers.store');
//});
//
//Route::group(['prefix' => 'payment', 'middleware' => 'auth'], function () {
//    Route::get('', 'PaymentController@index')->name('payment.index');
//    Route::get('/{id?}', 'PaymentController@index')->name('payment.index')->middleware('can:is-council');
//    Route::post('', 'PaymentController@store')->name('payment.store');
//    Route::post('/{id?}', 'PaymentController@write')->name('payment.store')->middleware('can:is-super');
//});

//Route::group(['prefix' => 'household', 'middleware' => 'auth'], function () {
//    Route::get('', 'HouseholdController@index')->name('household.index')->middleware('can:has-paid');
//    Route::get('/{id?}', 'HouseholdController@index')->name('household.index')->middleware('can:is-council');
//    Route::post('', 'HouseholdController@store')->name('household.store')->middleware('can:has-paid');
//    Route::post('/{id?}', 'HouseholdController@store')->name('household.store')->middleware('can:is-super');
//});
//
//Route::group(['prefix' => 'roomselection', 'middleware' => 'auth'], function () {
//    Route::get('/', 'RoomSelectionController@index')->name('roomselection.index')->middleware('can:has-paid');
//    Route::get('/{id?}', 'RoomSelectionController@index')->name('roomselection.index')->middleware('can:is-council');
//    Route::get('/assign/{id?}', 'RoomSelectionController@read')->name('roomselection.read')->middleware('can:is-council');
//    Route::post('/', 'RoomSelectionController@store')->name('roomselection.store')->middleware('can:has-paid');
//    Route::post('/{id?}', 'RoomSelectionController@store')->name('roomselection.store')->middleware('can:is-super');
//    Route::post('/assign/{id?}', 'RoomSelectionController@write')->name('roomselection.write')->middleware('can:is-super');
//    Route::get('/map', 'RoomSelectionController@map')->name('roomselection.map')->middleware('can:is-council');
//});
//
//Route::group(['prefix' => 'workshopchoice', 'middleware' => 'auth'], function () {
//    Route::get('/', 'WorkshopController@index')->name('workshopchoice.index')->middleware('can:has-paid');
//    Route::get('/{id?}', 'WorkshopController@index')->name('workshopchoice.index')->middleware('can:is-council');
//    Route::post('/', 'WorkshopController@store')->name('workshopchoice.store')->middleware('can:has-paid');
//    Route::post('/{id?}', 'WorkshopController@store')->name('workshopchoice.store')->middleware('can:is-super');
//});
//
//Route::group(['prefix' => 'nametag', 'middleware' => 'auth'], function () {
//    Route::get('/', 'NametagController@index')->name('nametag.index')->middleware('can:has-paid');
//    Route::post('/', 'NametagController@store')->name('nametag.store')->middleware('can:has-paid');
//    Route::get('/nametag/{i}/{id}', 'NametagController@read')->middleware('auth', 'role:admin|council');
//    Route::post('/nametag/f/{id}', 'NametagController@write')->middleware('auth', 'role:admin');
//});
//
//Route::group(['prefix' => 'data'], function () {
//    Route::get('loginsearch', 'DataController@loginsearch');
//    Route::get('camperlist', 'DataController@campers')->middleware('can:is-council');
//    Route::get('churchlist', 'DataController@churches')->middleware('auth');
//    Route::get('steps', 'DataController@steps')->middleware('can:has-paid');
//    Route::get('steps/{id?}', 'DataController@steps')->middleware('can:is-council');
//});
//Route::group(['middleware' => ['auth', 'can:is-council'], 'prefix' => 'reports'], function () {
//    Route::get('campers', 'ReportController@campers')->name('reports.campers');
//    Route::get('campers/{year}.xls', 'ReportController@campersExport')->name('reports.campers.export');
//    Route::get('chart', 'ReportController@chart')->name('reports.chart');
//    Route::get('conflicts', 'ReportController@conflicts');
//    Route::get('deposits', 'ReportController@deposits')->name('reports.deposits')->middleware('can:is-council');
//    Route::post('deposits/{id}', 'ReportController@depositsMark')->name('reports.deposits.mark')->middleware('can:is-super');
//    Route::get('firsttime', 'ReportController@firsttime');
//    Route::get('guarantee', 'ReportController@guarantee');
//    Route::get('outstanding', 'ReportController@outstanding')->name('reports.outstanding');
//    Route::get('outstanding/{filter?}', 'ReportController@outstanding');
//    Route::post('outstanding/{id}', 'ReportController@outstandingMark')->name('reports.outstanding.mark')->middleware('can:is-super');
//    Route::get('payments', 'ReportController@payments');
//    Route::get('payments/{year}.xls', 'ReportController@paymentsExport');
//    Route::get('payments/{year?}/name', 'ReportController@payments');
//    Route::get('programs', 'ReportController@programs')->name('reports.programs');
//    Route::get('rates', 'ReportController@rates');
//    Route::post('rates', 'ReportController@ratesMark')->middleware('auth', 'role:admin');;
//    Route::get('roommates', 'ReportController@roommates');
//    Route::get('rooms', 'ReportController@rooms')->name('reports.rooms');
//    Route::get('rooms/{year}.xls', 'ReportController@roomsExport');
//    Route::get('rooms/{year?}/name', 'ReportController@rooms');
//    Route::get('states', 'ReportController@states');
//    Route::get('unassigned', 'ReportController@unassigned');
//    Route::get('volunteers', 'ReportController@volunteers');
//    Route::get('workshops', 'ReportController@workshops')->name('reports.workshops');
//});

//Route::group(['middleware' => ['auth', 'can:is-council'], 'prefix' => 'tools'], function () {
//    Route::get('cognoscenti', 'ToolsController@cognoscenti')->name('tools.cognoscenti');
//    Route::get('nametags', 'ToolsController@nametagsList');
//    Route::post('nametags', 'ToolsController@nametagsPrint');
//    Route::get('nametags/all', 'ToolsController@nametags');
//    Route::get('nametags/{i}/{id}', 'ToolsController@nametagsFamily');
//    Route::get('programs', 'ToolsController@programIndex');
//    Route::post('programs', 'ToolsController@programStore');
//    Route::get('staffpositions', 'ToolsController@positionIndex')->name('tools.staff.index');
//    Route::post('staffpositions', 'ToolsController@positionStore')->name('tools.staff.store');
//    Route::get('workshops', 'ToolsController@workshopIndex');
//    Route::post('workshops', 'ToolsController@workshopStore');
//});
//
//Route::group(['middleware' => ['can:is-super'], 'prefix' => 'admin'], function () {
//    Route::get('distlist', 'AdminController@distlistIndex')->name('admin.distlist.index');
//    Route::post('distlist', 'AdminController@distlistExport')->name('admin.distlist.export');
//    Route::get('master', 'AdminController@masterIndex');
//    Route::post('master', 'AdminController@masterStore');
//    Route::get('massassign', 'AdminController@massAssignIndex');
//    Route::post('massassign/f/{id}', 'AdminController@massAssignStore');
//    Route::get('roles', 'AdminController@roleIndex')->name('admin.roles.index');
//    Route::post('roles', 'AdminController@roleStore')->name('admin.roles.store');
//    Route::get('positions', 'AdminController@positionIndex')->name('admin.positions.index');
//    Route::post('positions', 'AdminController@positionStore')->name('admin.positions.store');
//});
//
//Route::get('/brochure', function () {
//    $year = date('Y');
//    if (!is_file(public_path('MUUSA_' . $year . '_Brochure.pdf'))) $year--;
//    return redirect('MUUSA_' . $year . '_Brochure.pdf');
//})->name('brochure');
//Route::get('/troutlodge', function () {
//    return redirect('Trout_Lodge.pdf');
//})->name('troutlodgebrochure');

