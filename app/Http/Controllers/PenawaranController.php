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
    ->get();

    return view('pages.penawaran.indexs')
    ->with('url', 'penawaran')
    ->with('penawarans', $penawarans)
    ->with('top_menu_sel', 'menu_penawaran')
    ->with('page_title', 'Penawaran')
    ->with('page_description', 'Index');
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

    return view('pages.penawaran.create')
    ->with('url', 'penawaran')
    ->with('maxid', $maxid)
    ->with('inventory', $inventory)
    ->with('top_menu_sel', 'menu_penawaran')
    ->with('page_title', 'Penawaran')
    ->with('page_description', 'Create');
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
    ->get();

    return view('pages.penawaran.show')
    ->with('url', 'penawaran')
    ->with('penawarans', $penawarans)
    ->with('penawaran', $penawaran)
    ->with('top_menu_sel', 'menu_penawaran')
    ->with('page_title', 'Penawaran')
    ->with('page_description', 'Show');
  }

  public function edit($id)
  {
    $penawaran = Penawaran::find($id);
    $penawarans = Penawaran::where('penawaran.Penawaran', $penawaran -> Penawaran)
    ->get();
    $penawaran = $penawarans->first();

    return view('pages.penawaran.edit')
    ->with('url', 'penawaran')
    ->with('id', $id)
    ->with('penawarans', $penawarans)
    ->with('penawaran', $penawaran)
    ->with('top_menu_sel', 'menu_penawaran')
    ->with('page_title', 'Penawaran')
    ->with('page_description', 'Edit');
  }

  public function update(Request $request, $id)
  {
    $penawaran = Penawaran::find($id);

    $input = Input::all();
    $isisjkembali2s = $input['penawaranid'];
    foreach ($isisjkembali2s as $key => $isisjkembali)
    {
      Penawaran::where('id', $input['penawaranid'][$key])
      ->update([
        'Tgl' => $input['Tgl'],
        'PCode' => $input['PCode'],
        'Barang' => $input['Barang'][$key],
        'JS' => $input['JS'][$key],
        'Quantity' => $input['Quantity'][$key],
        'Amount' => str_replace(".","",substr($input['Amount'][$key], 3)),
        'ICode' => $input['ICode'][$key],
      ]);
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
