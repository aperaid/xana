<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\Supplier;
use Session;
use DB;
use Auth;

class DropdownController extends Controller
{
	public function ICodeList(Request $request){
    $code = Inventory::select('Code as label')
    ->where('Code', 'LIKE', '%'.$request->name.'%')
		->groupBy('Code')
    ->get();
    return $code;
  }
	
  public function InventoryList(Request $request){
    $barang = Inventory::select('Barang as label', 'id as key')
    ->where('Barang', 'LIKE', '%'.$request->name.'%')
    ->get();
    return $barang;
  }
	
	public function SupplierList(Request $request){
    $scode = Supplier::select('SCode as label')
    ->where('SCode', 'LIKE', '%'.$request->name.'%')
		->groupBy('SCode')
    ->get();
    return $scode;
  }
}
