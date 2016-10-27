<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use Session;
use DB;

class RegisterInventoryController extends Controller
{
    public function create()
    {
      $register = Inventory::orderby('id', 'desc')
      ->first();
      
    	return view('pages.registerinventory.create')
      ->with('register', $register)
      ->with('top_menu_sel', 'menu_registerinventory')
      ->with('page_title', 'Register Inventory')
      ->with('page_description', 'Create');
    }

    public function store(Request $request)
    {

      $inputs = $request->all();
    	$inputs['Price'] = str_replace(".","",substr($request->Price, 3));

    	$register = Inventory::Create($inputs);

    	return redirect()->route('viewinventory.index');
    }
}
