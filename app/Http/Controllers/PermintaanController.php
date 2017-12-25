<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Permintaan;
use App\PermintaanList;
use App\History;
use Session;
use DB;
use Auth;

class PermintaanController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Administrator'||Auth::user()->access=='PPNAdmin'||Auth::user()->access=='NonPPNAdmin'||Auth::user()->access=='Purchasing'||Auth::user()->access=='SuperPurchasing'))
				$this->access = array("index", "create", "show", "edit");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
  public function index(){
		$permintaans = Permintaan::all();
		
    if(in_array("index", $this->access)){
      return view('pages.permintaan.indexs')
      ->with('url', 'permintaan')
      ->with('permintaans', $permintaans)
      ->with('top_menu_sel', 'menu_permintaan')
      ->with('page_title', 'Permintaan')
      ->with('page_description', 'Index');
    }else
      return redirect()->back();
  }
  
  public function create(){
    /*$warehouse = Inventory::groupBy('Warehouse')
    ->orderBy('id', 'asc')
    ->pluck('Warehouse', 'Warehouse');*/
    
    if(in_array("create", $this->access)){
      return view('pages.permintaan.create')
      ->with('url', 'permintaan')
      //->with(compact('warehouse'))
      ->with('top_menu_sel', 'menu_permintaan')
      ->with('page_title', 'Permintaan')
      ->with('page_description', 'Create');
    }else
      return redirect()->back();
  }

  public function store(Request $request){
		//Validation
		$this->validate($request, [
			'MintaCode'=>'required|unique:permintaan',
			'Tgl'=>'required',
			'SCode'=>'required'
		], [
			'MintaCode.required' => 'The Minta Code field is required.',
			'MintaCode.unique' => 'The Minta Code has already been taken.',
			'Tgl.required' => 'The Date field is required.',
			'SCode.required' => 'The Supplier Code field is required.'
		]);
		
		$permintaan = new Permintaan;
		$permintaan->MintaCode = $request->MintaCode;
		$permintaan->Tgl = $request->Tgl;
		$permintaan->SCode = $request->SCode;
		$permintaan->save();
		
		$permintaanlists = $request->Barang;
		foreach ($permintaanlists as $key => $permintaanlist)
		{
			$permintaanlist = new PermintaanList;
			$permintaanlist->Quantity = $request->Quantity[$key];
			$permintaanlist->Amount = str_replace(".","",substr($request->Amount[$key], 3));
			$permintaanlist->ICode = $request->ICode[$key];
			$permintaanlist->MintaCode = $request->MintaCode;
			$permintaanlist->save();
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Permintaan on Code '.$request['Permintaan'];
		$history->save();
		
		return redirect()->route('permintaan.show', $permintaan->id)->with('message', 'Permintaan with Code '.$permintaan->MintaCode.' is  created');
  }

  public function show($id){
		$permintaan = Permintaan::find($id);
    $permintaanlists = PermintaanList::select('permintaanlist.*', 'permintaan.*', 'inventory.Barang', 'inventory.Type')
		->leftJoin('permintaan', 'permintaanlist.MintaCode', 'permintaan.MintaCode')
		->leftJoin('inventory', 'permintaanlist.ICode', 'inventory.Code')
		->where('permintaan.id', $id)
    ->orderBy('permintaan.id', 'asc')
    ->get();

    if(in_array("show", $this->access)){
      return view('pages.permintaan.show')
      ->with('url', 'permintaan')
      ->with('permintaan', $permintaan)
      ->with('permintaanlists', $permintaanlists)
      ->with('top_menu_sel', 'menu_permintaan')
      ->with('page_title', 'Permintaan')
      ->with('page_description', 'Show');
    }else
      return redirect()->back();
  }

  public function edit($id){
    $permintaan = Permintaan::find($id);
    $permintaanlists = PermintaanList::select('permintaanlist.*', 'permintaan.*', 'inventory.Barang', 'inventory.Type')
		->leftJoin('permintaan', 'permintaanlist.MintaCode', 'permintaan.MintaCode')
		->leftJoin('inventory', 'permintaanlist.ICode', 'inventory.Code')
		->where('permintaan.id', $id)
    ->orderBy('permintaan.id', 'asc')
    ->get();

    if(in_array("edit", $this->access)){
      return view('pages.permintaan.edit')
      ->with('url', 'permintaan')
      ->with('permintaan', $permintaan)
      ->with('permintaanlists', $permintaanlists)
      ->with('top_menu_sel', 'menu_permintaan')
      ->with('page_title', 'Permintaan')
      ->with('page_description', 'Edit');
    }else
      return redirect()->back();
  }

  public function update(Request $request, $id){
		//Validation
		$this->validate($request, [
			'MintaCode'=>'required|unique:permintaan,MintaCode,'.$request->OldPermintaan.',MintaCode',
			'Tgl'=>'required',
			'SCode'=>'required'
		], [
			'MintaCode.required' => 'The Minta Code field is required.',
			'MintaCode.unique' => 'The Minta Code has already been taken.',
			'Tgl.required' => 'The Date field is required.',
			'SCode.required' => 'The Supplier Code field is required.'
		]);
    
		$permintaan = Permintaan::find($id);
		$permintaan->MintaCode = $request->MintaCode;
		$permintaan->Tgl = $request->Tgl;
		$permintaan->SCode = $request->SCode;
		$permintaan->save();

    PermintaanList::where('MintaCode', $permintaan->MintaCode)->delete();
		DB::statement('ALTER TABLE permintaanlist auto_increment = 1;');
		
    $permintaanlists = $request->Barang;
    foreach ($permintaanlists as $key => $permintaanlist)
		{
			$permintaanlist = new PermintaanList;
			$permintaanlist->Quantity = $request->Quantity[$key];
			$permintaanlist->Amount = str_replace(".","",substr($request->Amount[$key], 3));
			$permintaanlist->ICode = $request->ICode[$key];
			$permintaanlist->MintaCode = $request->MintaCode;
			$permintaanlist->save();
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update Permintaan on Code '.$request['Permintaan'];
    $history->save();

    return redirect()->route('permintaan.show', $id);
  }

  public function DeletePermintaan(Request $request){
    Permintaan::where('MintaCode', $request->MintaCode)->delete();
		DB::statement('ALTER TABLE permintaan auto_increment = 1;');
    PermintaanList::where('MintaCode', $request->MintaCode)->delete();
		DB::statement('ALTER TABLE permintaanlist auto_increment = 1;');
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Permintaan on Code '.$request->MintaCode;
    $history->save();
    
    Session::flash('message', 'Permintaan with Code '.$request->MintaCode.' is deleted');
  }
}
