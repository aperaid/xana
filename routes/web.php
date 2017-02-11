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

Route::group(['middleware' => 'auth'], function () {

  Route::resource('customer', 'CustomerController');

  Route::resource('project', 'ProjectController');
  
  Route::resource('penawaran', 'PenawaranController');

  Route::resource('reference', 'ReferenceController');
  Route::post('/reference/customerproject', 'ReferenceController@StoreCustomerProject');
	Route::post('/reference/transportinvoice', 'ReferenceController@EditTransportInvoice');

  Route::get('po/create2/{id}', ['as' => 'po.create2', 'uses' => 'POController@getCreate2']);
  Route::post('po/create3/{id}', ['as' => 'po.create3', 'uses' => 'POController@getCreate3']);
  
  Route::resource('po', 'POController');

  Route::post('transaksi/extend', ['as' => 'transaksi.extend', 'uses' => 'TransaksiController@Extend']);
	Route::post('transaksi/extenddelete', ['as' => 'transaksi.extenddelete', 'uses' => 'TransaksiController@ExtendDelete']);
  Route::get('transaksi/claimcreate/{id}', ['as' => 'transaksi.claimcreate', 'uses' => 'TransaksiController@getClaim']);
  Route::post('transaksi/claimcreate2/{id}', ['as' => 'transaksi.claimcreate2', 'uses' => 'TransaksiController@getClaim2']);
  Route::post('transaksi/claimcreate3/{id}', ['as' => 'transaksi.claimcreate3', 'uses' => 'TransaksiController@getClaim3']);
  Route::post('transaksi/updateclaimcreate', ['as' => 'transaksi.updateclaimcreate', 'uses' =>'TransaksiController@postClaim']);
  Route::post('transaksi/claimdelete', ['as' => 'transaksi.claimdelete', 'uses' => 'TransaksiController@ClaimDelete']);

  Route::resource('transaksi', 'TransaksiController');

  Route::resource('sjkirim', 'SJKirimController');
  Route::post('sjkirim/create2/{id}', ['as' => 'sjkirim.create2', 'uses' => 'SJKirimController@getCreate2']);
  Route::post('sjkirim/create3/{id}', ['as' => 'sjkirim.create3', 'uses' => 'SJKirimController@getCreate3']);
  Route::get('sjkirim/qtertanda/{id}', ['as' => 'sjkirim.qtertanda', 'uses' => 'SJKirimController@getQTertanda']);
  Route::post('sjkirim/updateqtertanda/{id}', ['as' => 'sjkirim.updateqtertanda', 'uses' =>'SJKirimController@postQTertanda']);
  Route::get('sjkirim/SJ/{id}', ['as' => 'sjkirim.SJ', 'uses' => 'SJKirimController@getSJ']);
  Route::post('sjkirim/baranghilang/{id}', ['as' => 'sjkirim.baranghilang', 'uses' =>'SJKirimController@postBarangHilang']);

  Route::resource('sjkembali', 'SJKembaliController');
  Route::post('sjkembali/create2/{id}', ['as' => 'sjkembali.create2', 'uses' => 'SJKembaliController@getCreate2']);
  Route::post('sjkembali/create3/{id}', ['as' => 'sjkembali.create3', 'uses' => 'SJKembaliController@getCreate3']);
  Route::get('sjkembali/qterima/{id}', ['as' => 'sjkembali.qterima', 'uses' => 'SJKembaliController@getQTerima']);
  Route::post('sjkembali/updateqterima/{id}', ['as' => 'sjkembali.updateqterima', 'uses' =>'SJKembaliController@postQTerima']);
  Route::get('sjkembali/SPB/{id}', ['as' => 'sjkembali.SPB', 'uses' => 'SJKembaliController@getSPB']);
  Route::post('sjkembali/baranghilang/{id}', ['as' => 'sjkembali.baranghilang', 'uses' =>'SJKirimController@postBarangHilang']);
  
  Route::get('invoice/showsewa/{id}', ['as' => 'invoice.showsewa', 'uses' => 'InvoiceController@getInvoiceSewa']);
  Route::post('invoice/updateshowsewa/{id}', ['as' => 'invoice.updateshowsewa', 'uses' => 'InvoiceController@postInvoiceSewa']);
	Route::get('invoice/showsewapisah/{id}', ['as' => 'invoice.showsewapisah', 'uses' => 'InvoiceController@getInvoiceSewaPisah']);
	Route::post('invoice/updateshowsewapisah/{id}', ['as' => 'invoice.updateshowsewapisah', 'uses' => 'InvoiceController@postInvoiceSewaPisah']);
  Route::get('invoice/showjual/{id}', ['as' => 'invoice.showjual', 'uses' => 'InvoiceController@getInvoiceJual']);
  Route::post('invoice/updateshowjual/{id}', ['as' => 'invoice.updateshowjual', 'uses' => 'InvoiceController@postInvoiceJual']);
	Route::get('invoice/showjualpisah/{id}', ['as' => 'invoice.showjualpisah', 'uses' => 'InvoiceController@getInvoiceJualPisah']);
	Route::post('invoice/updateshowjualpisah/{id}', ['as' => 'invoice.updateshowjualpisah', 'uses' => 'InvoiceController@postInvoiceJualPisah']);
  Route::get('invoice/showclaim/{id}', ['as' => 'invoice.showclaim', 'uses' => 'InvoiceController@getInvoiceClaim']);
  Route::post('invoice/updateshowclaim/{id}', ['as' => 'invoice.updateshowclaim', 'uses' => 'InvoiceController@postInvoiceClaim']);
  Route::get('invoice/lunas/{id}', ['as' => 'invoice.lunas', 'uses' => 'InvoiceController@getLunas']);
  Route::post('invoice/updatelunas/{id}', ['as' => 'invoice.updatelunas', 'uses' => 'InvoiceController@postLunas']);
  Route::get('invoice/BA/{id}', ['as' => 'invoice.BA', 'uses' => 'InvoiceController@getBA']);
  Route::get('invoice/Invs/{id}', ['as' => 'invoice.Invs', 'uses' => 'InvoiceController@getInvs']);
  Route::get('invoice/Invj/{id}', ['as' => 'invoice.Invj', 'uses' => 'InvoiceController@getInvj']);
  Route::get('invoice/Invc/{id}', ['as' => 'invoice.Invc', 'uses' => 'InvoiceController@getInvc']);
  Route::get('invoice/Invst/{id}', ['as' => 'invoice.Invst', 'uses' => 'InvoiceController@getInvst']);
  Route::get('invoice/Invjt/{id}', ['as' => 'invoice.Invjt', 'uses' => 'InvoiceController@getInvjt']);

  Route::resource('invoice', 'InvoiceController');

  Route::get('inventory/viewinventory', ['as' => 'inventory.viewinventory', 'uses' => 'InventoryController@getView']);
  Route::get('inventory/adjustinventory', ['as' => 'inventory.adjustinventory', 'uses' => 'InventoryController@getAdjustment']);
  Route::get('inventory/editadjustinventory/{id}', ['as' => 'inventory.editadjustinventory', 'uses' => 'InventoryController@getEditAdjustment']);
  Route::post('inventory/updateadjustinventory/{id}', ['as' => 'inventory.updateadjustinventory', 'uses' => 'InventoryController@postEditAdjustment']);
  Route::get('inventory/transferinventory', ['as' => 'inventory.transferinventory', 'uses' => 'InventoryController@getTransfer']);
  Route::get('inventory/registerinventory', ['as' => 'inventory.registerinventory', 'uses' => 'InventoryController@getRegister']);
  Route::post('inventory/storeRegisterinventory', ['as' => 'inventory.storeRegisterinventory', 'uses' => 'InventoryController@postRegister']);
  Route::get('inventory/removeinventory', ['as' => 'inventory.removeinventory', 'uses' => 'InventoryController@remove']);
  Route::get('inventory/getremoveinventory/{id}', ['as' => 'inventory.getremoveinventory', 'uses' => 'InventoryController@getRemove']);
  Route::post('inventory/postremoveinventory/{id}', ['as' => 'inventory.postremoveinventory', 'uses' => 'InventoryController@postRemove']);

  Route::post('barang/', ['as' => 'barang/', 'uses' => 'BarangController@postQuantityAmount']);
});
