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
      $json = Inventory::select('inventory.Code', 'inventory.Jumlah', 'inventory.Price')
      ->where('inventory.Barang', $request->namabarang)
      ->where('inventory.Type', $request->tipebarang)
      ->first();
      
      return $json->toJson();
    }
}
