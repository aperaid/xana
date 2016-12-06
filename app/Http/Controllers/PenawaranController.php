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
  public function index()
  {
    $penawarans = Penawaran::select('penawaran.*', 'project.PCode')
    ->leftJoin('project', 'penawaran.PCode', '=', 'project.PCode')
    ->groupBy('penawaran.Penawaran')
    ->orderBy('id', 'asc')
    ->get();

    if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='POPPN'||Auth::user()->access()=='PONONPPN'){
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
    $penawaran = Penawaran::select([
      DB::raw('MAX(penawaran.id) AS maxid')
    ])
    ->first();
    
    if($penawaran -> maxid == 0){
      $maxid = 0;
    }else{
      $maxid = $penawaran -> maxid;
    }
    
    $inventory = Inventory::all();
    $warehouse = Inventory::groupBy('Warehouse')
    ->orderBy('id', 'asc')
    ->pluck('Warehouse', 'Warehouse');

    if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='POPPN'||Auth::user()->access()=='PONONPPN'){
      return view('pages.penawaran.create')
      ->with('url', 'penawaran')
      ->with('maxid', $maxid)
      ->with('inventory', $inventory)
      ->with(compact('warehouse'))
      ->with('top_menu_sel', 'menu_penawaran')
      ->with('page_title', 'Penawaran')
      ->with('page_description', 'Create');
    }else
      return redirect()->back();
  }

  public function store(Request $request)
  {
    $input = Input::all();
    $penawarans = $input['penawaranid'];
    foreach ($penawarans as $key => $penawaran)
    {
      $penawaran = new Penawaran;
      $penawaran->id = $input['penawaranid'][$key];
      $penawaran->Penawaran = $input['Penawaran'];
      $penawaran->Tgl = $input['Tgl'];
      $penawaran->Barang = $input['Barang'][$key];
      $penawaran->Warehouse = $input['Warehouse'][$key];
      $penawaran->JS = $input['JS'][$key];
      $penawaran->Quantity = $input['Quantity'][$key];
      $penawaran->Amount = str_replace(".","",substr($input['Amount'][$key], 3));
      $penawaran->PCode = $input['PCode'];
      $penawaran->ICode = $input['ICode'][$key];
      $penawaran->save();
    }
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Create Penawaran on Penawaran '.$request['Penawaran'];
    $history->save();
    
    return redirect()->route('penawaran.index');
  }

  public function show($id)
  {
    $penawaran = Penawaran::find($id);
    $penawarans = Penawaran::where('penawaran.Penawaran', $penawaran -> Penawaran)
    ->orderBy('id', 'asc')
    ->get();

    if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='POPPN'||Auth::user()->access()=='PONONPPN'){
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
    $penawarans = Penawaran::where('penawaran.Penawaran', $penawaran -> Penawaran)
    ->orderBy('id', 'asc')
    ->get();
    $maxpenawaran = Penawaran::select([
      'penawaran.*',
      DB::raw('MAX(penawaran.id) AS maxid')
    ])
    ->where('penawaran.Penawaran', $penawaran -> Penawaran)
    ->first();

    $warehouse = Inventory::groupBy('Warehouse')
    ->orderBy('id', 'asc')
    ->pluck('Warehouse', 'Warehouse');

    if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='POPPN'||Auth::user()->access()=='PONONPPN'){
      return view('pages.penawaran.edit')
      ->with('url', 'penawaran')
      ->with('id', $id)
      ->with('penawarans', $penawarans)
      ->with('maxpenawaran', $maxpenawaran)
      ->with(compact('warehouse'))
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
    
    $input = Input::all();
    $penawarans = $input['penawaranid'];
    foreach ($penawarans as $key => $penawaran)
    {
      $penawaran = new Penawaran;
      $penawaran->id = $input['penawaranid'][$key];
      $penawaran->Penawaran = $input['Penawaran'];
      $penawaran->Tgl = $input['Tgl'];
      $penawaran->Barang = $input['Barang'][$key];
      $penawaran->Warehouse = $input['Warehouse'][$key];
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
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Penawaran on Penawaran '.$request['penawaran->Penawaran'];
    $history->save();
    
    Session::flash('message', 'Delete is successful!');

    return redirect()->route('penawaran.index');
  }
}
