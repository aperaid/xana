<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use Session;
use DB;

class AdjustInventoryController extends Controller
{
    public function index()
    {
  		$adjust = Inventory::all();

    	return view('pages.adjustinventory.indexs')
  		->with('adjusts', $adjust)
      ->with('top_menu_sel', 'menu_adjustment')
  		->with('page_title', 'Adjust Inventory')
  		->with('page_description', 'Index');
    }

    public function edit($id)
    {
    	$adjust = Inventory::find($id);

    	return view('pages.adjustinventory.edit')
      ->with('adjust', $adjust)
      ->with('top_menu_sel', 'menu_adjustment')
      ->with('page_title', 'Adjust Inventory')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
    	$adjust = Inventory::find($id);

    	$adjust->Price = str_replace(".","",substr($request->Price, 3));
    	$adjust->Jumlah = $request->Jumlah;
      $adjust->Type = $request->Type;
      $adjust->Warehouse = $request->Warehouse;
    	$adjust->save();

    	return redirect()->route('adjustinventory.index', $id);
    }
}
