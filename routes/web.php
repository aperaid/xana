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

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {

	Route::get('/customer', 'CustomerController@index')->name('customer.index');
	Route::get('/customer/show/{id?}', 'CustomerController@ShowCustomer')->name('customer.show');
	Route::get('/customer/create', 'CustomerController@CreateCustomer')->name('customer.create');
	Route::post('/customer/store', 'CustomerController@StoreCustomer')->name('customer.store');
	Route::get('/customer/edit/{id?}', 'CustomerController@EditCustomer')->name('customer.edit');
	Route::post('/customer/update', 'CustomerController@UpdateCustomer')->name('customer.update');
	Route::post('/customer/delete', 'CustomerController@DeleteCustomer')->name('customer.destroy');

  Route::resource('project', 'ProjectController');
	Route::post('/project/delete', 'ProjectController@DeleteProject')->name('project.destroy');
  
  Route::resource('penawaran', 'PenawaranController');
	Route::post('/penawaran/delete', 'PenawaranController@DeletePenawaran')->name('penawaran.destroy');

  Route::resource('reference', 'ReferenceController');
	Route::post('/reference/delete', 'ReferenceController@DeleteReference')->name('reference.destroy');
  Route::post('/reference/customerproject', 'ReferenceController@StoreCustomerProject');
	Route::post('/reference/transportinvoice', 'ReferenceController@EditTransportInvoice');

  Route::get('po/create2/{id}', ['as' => 'po.create2', 'uses' => 'POController@getCreate2']);
  Route::post('po/create3/{id}', ['as' => 'po.create3', 'uses' => 'POController@getCreate3']);
  
  Route::resource('po', 'POController');

  Route::post('transaksi/extend', ['as' => 'transaksi.extend', 'uses' => 'TransaksiController@Extend']);
	Route::post('transaksi/extenddelete', ['as' => 'transaksi.extenddelete', 'uses' => 'TransaksiController@ExtendDelete']);
	Route::post('transaksi/hilang', ['as' => 'transaksi.hilang', 'uses' => 'TransaksiController@Hilang']);
	Route::post('transaksi/cancelhilang', ['as' => 'transaksi.cancelhilang', 'uses' => 'TransaksiController@CancelHilang']);
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
	Route::get('invoice/showsewakirim/{id}', ['as' => 'invoice.showsewakirim', 'uses' => 'InvoiceController@getInvoiceSewaKirim']);
	Route::post('invoice/updateshowsewakirim/{id}', ['as' => 'invoice.updateshowsewakirim', 'uses' => 'InvoiceController@postInvoiceSewaKirim']);
  Route::get('invoice/showjual/{id}', ['as' => 'invoice.showjual', 'uses' => 'InvoiceController@getInvoiceJual']);
  Route::post('invoice/updateshowjual/{id}', ['as' => 'invoice.updateshowjual', 'uses' => 'InvoiceController@postInvoiceJual']);
	Route::get('invoice/showjualpisah/{id}', ['as' => 'invoice.showjualpisah', 'uses' => 'InvoiceController@getInvoiceJualPisah']);
	Route::post('invoice/updateshowjualpisah/{id}', ['as' => 'invoice.updateshowjualpisah', 'uses' => 'InvoiceController@postInvoiceJualPisah']);
	Route::get('invoice/showjualkirim/{id}', ['as' => 'invoice.showjualkirim', 'uses' => 'InvoiceController@getInvoiceJualKirim']);
	Route::post('invoice/updateshowjualkirim/{id}', ['as' => 'invoice.updateshowjualkirim', 'uses' => 'InvoiceController@postInvoiceJualKirim']);
  Route::get('invoice/showclaim/{id}', ['as' => 'invoice.showclaim', 'uses' => 'InvoiceController@getInvoiceClaim']);
  Route::post('invoice/updateshowclaim/{id}', ['as' => 'invoice.updateshowclaim', 'uses' => 'InvoiceController@postInvoiceClaim']);
  Route::post('invoice/updatelunas', ['as' => 'invoice.updatelunas', 'uses' => 'InvoiceController@postLunas']);
  Route::get('invoice/BAS/{id}', ['as' => 'invoice.BAS', 'uses' => 'InvoiceController@getBAS']);
	Route::get('invoice/BAPisah/{id}', ['as' => 'invoice.BAPisah', 'uses' => 'InvoiceController@getBAPisah']);
	Route::get('invoice/BAKirim/{id}', ['as' => 'invoice.BAKirim', 'uses' => 'InvoiceController@getBAKirim']);
  Route::get('invoice/Invs/{id}', ['as' => 'invoice.Invs', 'uses' => 'InvoiceController@getInvs']);
	Route::get('invoice/InvsPisah/{id}', ['as' => 'invoice.InvsPisah', 'uses' => 'InvoiceController@getInvsPisah']);
	Route::get('invoice/InvsKirim/{id}', ['as' => 'invoice.InvsKirim', 'uses' => 'InvoiceController@getInvsKirim']);
  Route::get('invoice/Invst/{id}', ['as' => 'invoice.Invst', 'uses' => 'InvoiceController@getInvst']);
  Route::get('invoice/InvstPisah/{id}', ['as' => 'invoice.InvstPisah', 'uses' => 'InvoiceController@getInvstPisah']);
  Route::get('invoice/InvstKirim/{id}', ['as' => 'invoice.InvstKirim', 'uses' => 'InvoiceController@getInvstKirim']);
	Route::get('invoice/BAJ/{id}', ['as' => 'invoice.BAJ', 'uses' => 'InvoiceController@getBAJ']);
  Route::get('invoice/Invj/{id}', ['as' => 'invoice.Invj', 'uses' => 'InvoiceController@getInvj']);
	Route::get('invoice/InvjPisah/{id}', ['as' => 'invoice.InvjPisah', 'uses' => 'InvoiceController@getInvjPisah']);
	Route::get('invoice/InvjKirim/{id}', ['as' => 'invoice.InvjKirim', 'uses' => 'InvoiceController@getInvjKirim']);
  Route::get('invoice/Invjt/{id}', ['as' => 'invoice.Invjt', 'uses' => 'InvoiceController@getInvjt']);
	Route::get('invoice/InvjtPisah/{id}', ['as' => 'invoice.InvjtPisah', 'uses' => 'InvoiceController@getInvjtPisah']);
	Route::get('invoice/InvjtKirim/{id}', ['as' => 'invoice.InvjtKirim', 'uses' => 'InvoiceController@getInvjtKirim']);
  Route::get('invoice/BAC/{id}', ['as' => 'invoice.BAC', 'uses' => 'InvoiceController@getBAC']);
  Route::get('invoice/Invc/{id}', ['as' => 'invoice.Invc', 'uses' => 'InvoiceController@getInvc']);

  Route::resource('invoice', 'InvoiceController');

	Route::get('inventory/stockproject', ['as' => 'inventory.stockproject', 'uses' => 'InventoryController@getInventoryProject']);
	Route::get('inventory/viewstockproject/{id}', ['as' => 'inventory.viewstockproject', 'uses' => 'InventoryController@getViewInventoryProject']);
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
	
	Route::get('/user/{id?}', 'UserController@Users');
	Route::post('/user/edit', 'UserController@EditUsers');
	Route::post('/user/password', 'UserController@PasswordUsers');
	Route::post('/user/delete', 'UserController@DeleteUsers');
	Route::get('/add/user', 'UserController@AddUsers');
	Route::post('/add/user', 'UserController@NewUsers');
	
	Route::get('/supplier', 'SupplierController@index')->name('supplier.index');
	Route::get('/supplier/show/{id?}', 'SupplierController@ShowSupplier')->name('supplier.show');
	Route::get('/supplier/create', 'SupplierController@CreateSupplier')->name('supplier.create');
	Route::post('/supplier/store', 'SupplierController@StoreSupplier')->name('supplier.store');
	Route::get('/supplier/edit/{id?}', 'SupplierController@EditSupplier')->name('supplier.edit');
	Route::post('/supplier/update', 'SupplierController@UpdateSupplier')->name('supplier.update');
	Route::post('/supplier/delete', 'SupplierController@DeleteSupplier')->name('supplier.destroy');
	
	Route::resource('permintaan', 'PermintaanController');
	Route::post('/permintaan/delete', 'PermintaanController@DeletePermintaan')->name('permintaan.destroy');

	Route::resource('pemesanan', 'PemesananController');
	Route::post('/pemesanan/delete', 'PemesananController@DeletePemesanan')->name('pemesanan.destroy');
	
  Route::resource('penerimaan', 'PenerimaanController');
	Route::post('/penerimaan/delete', 'PenerimaanController@DeletePenerimaan')->name('penerimaan.destroy');
	
  Route::resource('retur', 'ReturController');
	Route::post('/retur/delete', 'ReturController@DeleteRetur')->name('retur.destroy');
	
	Route::resource('purchaseinvoice', 'PurchaseInvoiceController');
  Route::post('purchaseinvoice/updatelunas', ['as' => 'purchaseinvoice.updatelunas', 'uses' => 'PurchaseInvoiceController@postLunas']);
  Route::get('purchaseinvoice/invoice/{id}', ['as' => 'purchaseinvoice.invoice', 'uses' => 'PurchaseInvoiceController@getInvoice']);
/*
 * item info
 */
Route::post('barang/', ['as' => 'barang/', 'uses' => 'BarangController@postQuantityAmount']);
Route::post('barang/beli/', ['as' => 'barang/beli', 'uses' => 'BarangController@postPurchaseAmount']);
//Route::get('/barang/beli/{id}', 'BarangController@postPurchaseAmount');
	
/*
 * dropdown
 */
Route::post('/dropdown/icodelist/{name}', 'DropdownController@ICodeList');
Route::post('/dropdown/inventorylist/{name}', 'DropdownController@InventoryList');
Route::post('/dropdown/supplierlist/{name}', 'DropdownController@SupplierList');
});
