<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use Session;
use DB;
use Auth;

class BarangController extends Controller
{
  public function postQuantityAmount(Request $request)
    {
      $json = Inventory::select('inventory.Code', 'inventory.JualPrice', 'inventory.Price', 'inventory.Kumbang', 'inventory.BulakSereh', 'inventory.Legok', 'inventory.CitraGarden')
      ->where('inventory.Barang', $request->namabarang)
      ->where('inventory.Type', $request->tipebarang)
      ->first();
      
      return $json->toJson();
    }
		
	public function postPurchaseAmount(Request $request)
	{
		$json = Inventory::select('inventory.Type', 'inventory.Code', 'inventory.BeliPrice', 'inventory.Warehouse')
		->where('inventory.id', $request->id)
		->where('inventory.Barang', $request->namabarang)
		->first();
		
		return $json->toJson();
	}
}
