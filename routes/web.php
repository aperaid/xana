<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::resource('customer', 'CustomerController');

Route::resource('project', 'ProjectController');

Route::resource('reference', 'ReferenceController');

Route::resource('po', 'POController');

Route::resource('transaksi', 'TransaksiController');

Route::resource('extend', 'ExtendController');

Route::resource('sjkirim', 'SJKirimController');

Route::resource('sjkembali', 'SJKembaliController');

Route::resource('claim', 'ClaimController');

Route::get('invoice/showsewa', 'InvoiceController@getInvoiceSewa');

Route::resource('invoice', 'InvoiceController');

Route::resource('viewinventory', 'ViewInventoryController');

Route::resource('adjustinventory', 'AdjustInventoryController');

Route::resource('registerinventory', 'RegisterInventoryController');

Route::get('searchajax',array('as'=>'searchajax','uses'=>'AutoCompleteController@autoComplete'));

Route::get('autocomplete', 'SearchController@autocomplete');