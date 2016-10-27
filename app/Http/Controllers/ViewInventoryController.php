<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use Session;
use DB;

class ViewInventoryController extends Controller
{
    public function index()
    {
  		$inventory = Inventory::all();

    	return view('pages.viewinventory.indexs')
  		->with('inventorys', $inventory)
      ->with('top_menu_sel', 'menu_inventory')
  		->with('page_title', 'Inventory')
  		->with('page_description', 'Index');
    }
}
