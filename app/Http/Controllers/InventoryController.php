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

    $adjust->JualPrice = str_replace(".","",substr($request->JualPrice, 3));
    $adjust->Price = str_replace(".","",substr($request->Price, 3));
    $adjust->Kumbang = $request->Kumbang;
    $adjust->BulakSereh = $request->BulakSereh;
    $adjust->Legok = $request->Legok;
    $adjust->CitraGarden = $request->CitraGarden;
    $adjust->save();
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Adjust inventory on Inventory code '.$request['Code'];
    $history->save();

    return redirect()->route('inventory.adjustinventory');
  }
  
  public function getTransfer()
  {
    $transfer = Inventory::orderby('id', 'desc')
    ->first();
    
    return view('pages.inventory.transferinventory')
    ->with('url', 'transferinventory')
    ->with('transfer', $transfer)
    ->with('top_menu_sel', 'menu_transfer')
    ->with('page_title', 'Transfer Inventory')
    ->with('page_description', 'Index');
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
    $maxinventory = Inventory::select([
      'inventory.*',
      DB::raw('MAX(inventory.id) AS maxid')
    ])
    ->first();
    
    $input = $request->all();
    $type = array("NEW", "SECOND");
    $code = array($input['Code']."B", $input['Code']."L");
    $kumbang = array($input['Kumbang'], 0);
    $bulaksereh = array($input['BulakSereh'], 0);
    $legok = array($input['Legok'], 0);
    $citragarden = array($input['CitraGarden'], 0);
    
    $is_exist = Inventory::where('Code', $request->Code.'B')->first();
    if(isset($is_exist->Code)){
      return redirect()->route('inventory.registerinventory')->with('error', 'Inventory with Code '.strtoupper($request->Code).' is already exist!');
    }else{
      $inventories = $type;
      foreach ($inventories as $key => $inventory)
      {
        $inventory = new Inventory;
        $inventory->id = $maxinventory->maxid+$key+1;
        $inventory->Code = $code[$key];
        $inventory->Barang = $input['Barang'];
        $inventory->JualPrice = str_replace(".","",substr($request->JualPrice, 3));
        $inventory->Price = str_replace(".","",substr($request->Price, 3));
        $inventory->Type = $type[$key];
        $inventory->Kumbang = $kumbang;
        $inventory->BulakSereh = $bulaksereh;
        $inventory->Legok = $legok;
        $inventory->CitraGarden = $citragarden;
        $inventory->save();
      }
    }
    
    /*$inputs['JualPrice'] = str_replace(".","",substr($request->JualPrice, 3));
    $inputs['Price'] = str_replace(".","",substr($request->Price, 3));
    $register = Inventory::Create($inputs);*/

    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Register inventory on Inventory code '.$request['Code'];
    $history->save();
    
    return redirect()->route('inventory.viewinventory');
  }
  
  public function remove()
  {
    $removes = Inventory::groupBy('Barang')
    ->get();

    return view('pages.inventory.removeinventory')
    ->with('url', 'removeinventory')
    ->with('removes', $removes)
    ->with('top_menu_sel', 'menu_remove')
    ->with('page_title', 'Remove Inventory')
    ->with('page_description', 'Index');
  }
  
  public function getRemove($id)
    {
      return view('pages.inventory.getremoveinventory')
      ->with('id', $id);
    }
  
  public function postRemove($id)
  {
    $inventory = Inventory::find($id);
    
    Inventory::where('Barang', $inventory->Barang)->delete();
		DB::statement('ALTER TABLE inventory auto_increment = 1;');
    
    return redirect()->route('inventory.removeinventory');
  }
}
