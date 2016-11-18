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

Route::get('home', 'HomeController@index');

Route::resource('customer', 'CustomerController');

Route::resource('project', 'ProjectController');

Route::resource('reference', 'ReferenceController');

Route::resource('po', 'POController');

Route::get('transaksi/extend/{id}', ['as' => 'transaksi.extend', 'uses' => 'TransaksiController@getExtend']);
Route::post('transaksi/updateExtend/{id}', ['as' => 'transaksi.updateExtend', 'uses' =>'TransaksiController@postExtend']);
Route::get('transaksi/claimcreate/{id}', ['as' => 'transaksi.claimcreate', 'uses' => 'TransaksiController@getClaim']);
Route::post('transaksi/updateClaimCreate/{id}', ['as' => 'transaksi.updateClaimCreate', 'uses' =>'TransaksiController@postClaim']);

Route::resource('transaksi', 'TransaksiController');

Route::get('sjkirim/qtertanda/{id}', ['as' => 'sjkirim.qtertanda', 'uses' => 'SJKirimController@getQTertanda']);
Route::post('sjkirim/updateqtertanda/{id}', ['as' => 'sjkirim.updateqtertanda', 'uses' =>'SJKirimController@postQTertanda']);

Route::post('sjkirim/create2/{id}', ['as' => 'sjkirim.create2', 'uses' => 'SJKirimController@getCreate2']);
Route::post('sjkirim/create3/{id}', ['as' => 'sjkirim.create3', 'uses' => 'SJKirimController@getCreate3']);

Route::resource('sjkirim', 'SJKirimController');

Route::post('sjkembali/create2/{id}', ['as' => 'sjkembali.create2', 'uses' => 'SJKembaliController@getCreate2']);
Route::post('sjkembali/create3/{id}', ['as' => 'sjkembali.create3', 'uses' => 'SJKembaliController@getCreate3']);

Route::get('sjkembali/qterima/{id}', ['as' => 'sjkembali.qterima', 'uses' => 'SJKembaliController@getQTerima']);
Route::post('sjkembali/updateqterima/{id}', ['as' => 'sjkembali.updateqterima', 'uses' =>'SJKembaliController@postQTerima']);

Route::resource('sjkembali', 'SJKembaliController');

Route::resource('claim', 'ClaimController');

Route::get('invoice/showsewa/{id}', ['as' => 'invoice.showsewa', 'uses' => 'InvoiceController@getInvoiceSewa']);
Route::post('invoice/updateshowsewa/{id}', ['as' => 'invoice.updateshowsewa', 'uses' => 'InvoiceController@postInvoiceSewa']);
Route::get('invoice/showjual/{id}', ['as' => 'invoice.showjual', 'uses' => 'InvoiceController@getInvoiceJual']);
Route::post('invoice/updateshowjual/{id}', ['as' => 'invoice.updateshowjual', 'uses' => 'InvoiceController@postInvoiceJual']);
Route::get('invoice/showclaim/{id}', ['as' => 'invoice.showclaim', 'uses' => 'InvoiceController@getInvoiceClaim']);
Route::post('invoice/updateshowclaim/{id}', ['as' => 'invoice.updateshowclaim', 'uses' => 'InvoiceController@postInvoiceClaim']);

Route::resource('invoice', 'InvoiceController');

Route::get('inventory/viewinventory', ['as' => 'inventory.viewinventory', 'uses' => 'InventoryController@getView']);
Route::get('inventory/adjustinventory', ['as' => 'inventory.adjustinventory', 'uses' => 'InventoryController@getAdjustment']);
Route::get('inventory/editadjustinventory/{id}', ['as' => 'inventory.editadjustinventory', 'uses' => 'InventoryController@getEditAdjustment']);
Route::post('inventory/updateadjustinventory/{id}', ['as' => 'inventory.updateadjustinventory', 'uses' => 'InventoryController@postEditAdjustment']);
Route::get('inventory/registerinventory', ['as' => 'inventory.registerinventory', 'uses' => 'InventoryController@getRegister']);
Route::post('inventory/storeRegisterinventory', ['as' => 'inventory.storeRegisterinventory', 'uses' => 'InventoryController@postRegister']);

Route::get('searchajax',array('as'=>'searchajax','uses'=>'AutoCompleteController@autoComplete'));

Route::get('autocomplete', 'SearchController@autocomplete');