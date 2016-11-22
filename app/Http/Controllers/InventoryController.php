<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\History;
use Session;
use DB;
use Auth;

class InventoryController extends Controller
{
  public function getView()
  {
    $inventory = Inventory::all();

    return view('pages.inventory.viewinventory')
    ->with('url', 'viewinventory')
    ->with('inventorys', $inventory)
    ->with('top_menu_sel', 'menu_view')
    ->with('page_title', 'Inventory')
    ->with('page_description', 'Index');
  }
  
  public function getAdjustment()
  {
    $adjust = Inventory::all();

    return view('pages.inventory.adjustinventory')
    ->with('url', 'adjustinventory')
    ->with('adjusts', $adjust)
    ->with('top_menu_sel', 'menu_adjustment')
    ->with('page_title', 'Adjust Inventory')
    ->with('page_description', 'Index');
  }

  public function getEditAdjustment($id)
  {
    $adjust = Inventory::find($id);

    return view('pages.inventory.editadjustinventory')
    ->with('url', 'adjustinventory')
    ->with('adjust', $adjust)
    ->with('top_menu_sel', 'menu_adjustment')
    ->with('page_title', 'Adjust Inventory')
    ->with('page_description', 'Edit');
  }

  public function postEditAdjustment(Request $request, $id)
  {
    $adjust = Inventory::find($id);

    $adjust->Price = str_replace(".","",substr($request->Price, 3));
    $adjust->Jumlah = $request->Jumlah;
    $adjust->Type = $request->Type;
    $adjust->Warehouse = $request->Warehouse;
    $adjust->save();
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Adjust inventory on Inventory code '.$request['Code'];
    $history->save();

    return redirect()->route('inventory.adjustinventory');
  }
  
  public function getRegister()
  {
    $register = Inventory::orderby('id', 'desc')
    ->first();
    
    return view('pages.inventory.registerinventory')
    ->with('url', 'registerinventory')
    ->with('register', $register)
    ->with('top_menu_sel', 'menu_register')
    ->with('page_title', 'Register Inventory')
    ->with('page_description', 'Create');
  }

  public function postRegister(Request $request)
  {

    $inputs = $request->all();
    $inputs['Price'] = str_replace(".","",substr($request->Price, 3));

    $register = Inventory::Create($inputs);

    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Register inventory on Inventory code '.$request['Code'];
    $history->save();
    
    return redirect()->route('inventory.viewinventory');
  }
}
