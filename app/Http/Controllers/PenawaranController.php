<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Penawaran;
use App\Inventory;
use App\History;
use Session;
use DB;
use Auth;

class PenawaranController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Admin'||Auth::user()->access=='CUSTINVPPN'||Auth::user()->access=='CUSTINVNONPPN'))
				$this->access = array("index", "create", "show", "edit");
			else
				$this->access = array("");
		
		if(Auth::user()->access=='POINVPPN'||Auth::user()->access=='CUSTINVPPN')
				$this->PPNNONPPN = 1;
			else if(Auth::user()->access=='POINVNONPPN'||Auth::user()->access=='CUSTINVNONPPN')
				$this->PPNNONPPN = 0;
    return $next($request);
    });
	}
	
  public function index()
  {
		if(Auth::user()->access == 'Admin'){
			$penawarans = Penawaran::select('penawaran.*', 'project.PCode')
			->leftJoin('project', 'penawaran.PCode', '=', 'project.PCode')
			->groupBy('penawaran.Penawaran')
			->orderBy('id', 'asc')
			->get();
		}else{
			$penawarans = Penawaran::select('penawaran.*', 'project.PCode', 'customer.PPN')
			->leftJoin('project', 'penawaran.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('PPN', $this->PPNNONPPN)
			->groupBy('penawaran.Penawaran')
			->orderBy('id', 'asc')
			->get();
		}
		
    if(in_array("index", $this->access)){
      return view('pages.penawaran.indexs')
      ->with('url', 'penawaran')
      ->with('penawarans', $penawarans)
      ->with('top_menu_sel', 'menu_penawaran')
      ->with('page_title', 'Penawaran')
      ->with('page_description', 'Index');
    }else
      return redirect()->back();
  }
  
  public function create()
  {
    $last_penawaranid = Penawaran::max('id')+0;

    $inventory = Inventory::all();
    
    /*$warehouse = Inventory::groupBy('Warehouse')
    ->orderBy('id', 'asc')
    ->pluck('Warehouse', 'Warehouse');*/
    
    if(in_array("create", $this->access)){
      return view('pages.penawaran.create')
      ->with('url', 'penawaran')
      ->with('last_penawaranid', $last_penawaranid)
      ->with('inventory', $inventory)
      //->with(compact('warehouse'))
      ->with('top_menu_sel', 'menu_penawaran')
      ->with('page_title', 'Penawaran')
      ->with('page_description', 'Create');
    }else
      return redirect()->back();
  }

  public function store(Request $request)
  {
    $input = Input::all();
		$forgetpenawaran = $request->penawaranid[0];
		if($forgetpenawaran==null){
			return redirect()->route('penawaran.create')->with('error', 'Please add item first!');
		}else{
			$penawarans = $input['penawaranid'];
			$is_exist = Penawaran::where('Penawaran', $request->Penawaran)->first();
			if(isset($is_exist->Penawaran)){
				return redirect()->route('penawaran.create')->with('error', 'Penawaran with Penawaran Code '.$request->Penawaran.' is already exist!');
			}else{
				foreach ($penawarans as $key => $penawaran)
				{
					$penawaran = new Penawaran;
					$penawaran->id = $input['penawaranid'][$key];
					$penawaran->Penawaran = $input['Penawaran'];
					$penawaran->Tgl = $input['Tgl'];
					$penawaran->Barang = $input['Barang'][$key];
					$penawaran->JS = $input['JS'][$key];
					$penawaran->Quantity = $input['Quantity'][$key];
					$penawaran->Amount = str_replace(".","",substr($input['Amount'][$key], 3));
					$penawaran->PCode = $input['PCode'];
					$penawaran->ICode = $input['ICode'][$key];
					$penawaran->save();
				}
			}
			
			$history = new History;
			$history->User = Auth::user()->name;
			$history->History = 'Create Penawaran on Penawaran '.$request['Penawaran'];
			$history->save();
			
			return redirect()->route('penawaran.index');
		}
  }

  public function show($id)
  {
    $penawaran = Penawaran::find($id);
    $penawarans = Penawaran::where('penawaran.Penawaran', $penawaran -> Penawaran)
    ->orderBy('id', 'asc')
    ->get();

    if(in_array("show", $this->access)){
      return view('pages.penawaran.show')
      ->with('url', 'penawaran')
      ->with('penawarans', $penawarans)
      ->with('penawaran', $penawaran)
      ->with('top_menu_sel', 'menu_penawaran')
      ->with('page_title', 'Penawaran')
      ->with('page_description', 'Show');
    }else
      return redirect()->back();
  }

  public function edit($id)
  {
    $penawaran = Penawaran::find($id);
    $penawarans = Penawaran::select('penawaran.*', 'inventory.Type')
    ->leftJoin('inventory', 'penawaran.ICode', '=', 'inventory.Code')
    ->where('penawaran.Penawaran', $penawaran -> Penawaran)
    ->orderBy('penawaran.id', 'asc')
    ->get();
    $maxpenawaran = Penawaran::select([
      'penawaran.*',
      DB::raw('MAX(penawaran.id) AS maxid')
    ])
    ->where('penawaran.Penawaran', $penawaran -> Penawaran)
    ->first();

    if(in_array("edit", $this->access)){
      return view('pages.penawaran.edit')
      ->with('url', 'penawaran')
      ->with('id', $id)
      ->with('penawarans', $penawarans)
      ->with('maxpenawaran', $maxpenawaran)
      ->with('top_menu_sel', 'menu_penawaran')
      ->with('page_title', 'Penawaran')
      ->with('page_description', 'Edit');
    }else
      return redirect()->back();
  }

  public function update(Request $request, $id)
  {
    $penawaran = Penawaran::find($id);

    Penawaran::where('Penawaran', $penawaran->Penawaran)->delete();
		DB::statement('ALTER TABLE penawaran auto_increment = 1;');
    
    $input = Input::all();
    $penawarans = $input['penawaranid'];
    foreach ($penawarans as $key => $penawaran)
    {
      $penawaran = new Penawaran;
      $penawaran->id = $input['penawaranid'][$key];
      $penawaran->Penawaran = $input['Penawaran'];
      $penawaran->Tgl = $input['Tgl'];
      $penawaran->Barang = $input['Barang'][$key];
      $penawaran->JS = $input['JS'][$key];
      $penawaran->Quantity = $input['Quantity'][$key];
      $penawaran->Amount = str_replace(".","",substr($input['Amount'][$key], 3));
      $penawaran->PCode = $input['PCode'];
      $penawaran->ICode = $input['ICode'][$key];
      $penawaran->save();
    }
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update Penawaran on Penawaran '.$request['Penawaran'];
    $history->save();

    return redirect()->route('penawaran.show', $id);
  }

  public function destroy(Request $request, $id)
  {
    $penawaran = Penawaran::find($id);
    $penawarans = Penawaran::where('penawaran.Penawaran', $penawaran->Penawaran);
    $penawaranid = $penawarans->pluck('id');
    
    Penawaran::whereIn('id', $penawaranid)->delete();
		DB::statement('ALTER TABLE penawaran auto_increment = 1;');
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Penawaran on Penawaran '.$request['penawaran->Penawaran'];
    $history->save();
    
    Session::flash('message', 'Delete is successful!');

    return redirect()->route('penawaran.index');
  }
}
