<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pemesanan;
use App\PemesananList;
use App\Retur;
use App\History;
use Session;
use DB;
use Auth;

class ReturController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Administrator'||Auth::user()->access=='PPNAdmin'||Auth::user()->access=='NonPPNAdmin'||Auth::user()->access=='Purchasing'||Auth::user()->access=='SuperPurchasing'))
				$this->access = array("index", "create", "create2", "create3", "show", "edit");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
	public function index(){
		$returs = Retur::select('retur.*', 'supplier.Company')
		->leftJoin('pemesanan', 'retur.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->get();
		
		if(in_array("index", $this->access)){
			return view('pages.retur.indexs')
			->with('url', 'retur')
			->with('returs', $returs)
			->with('top_menu_sel', 'menu_retur')
			->with('page_title', 'Retur')
			->with('page_description', 'Index');
		}else
      return redirect()->back();
	}
	
  public function create(){
		$pemesanans = Pemesanan::select('pemesanan.PesanCode', 'pemesananlist.*', 'inventory.Barang', 'inventory.Type')
		->leftJoin('pemesananlist', 'pemesanan.PesanCode', 'pemesananlist.PesanCode')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesanan.id', Input::get('id'))
		->get();
		
    if(in_array("create", $this->access)){
      return view('pages.retur.create')
      ->with('url', 'retur')
			->with('pemesanans', $pemesanans)
      ->with('top_menu_sel', 'menu_retur')
      ->with('page_title', 'Retur')
      ->with('page_description', 'Create');
    }else
        return redirect()->back();
  }

  public function store(Request $request){
		//Validation
		$this->validate($request, [
			'ReturCode'=>'required|unique:retur',
			'Tgl'=>'required',
			'PesanCode'=>'required'
		], [
			'ReturCode.required' => 'The Retur Code field is required.',
			'ReturCode.unique' => 'The Retur Code has already been taken.',
			'Tgl.required' => 'The Date field is required.',
			'PesanCode.required' => 'The Pesan Code field is required.'
		]);
		
		$retur = new Retur;
		$retur->ReturCode = $request->ReturCode;
		$retur->Tgl = $request->Tgl;
		$retur->Transport = str_replace(".","",substr($request->Transport, 3));
		$retur->PesanCode = $request->PesanCode;
		$retur->save();
		
		$pemesanans = $request->Id;
		foreach ($pemesanans as $key => $pemesanan)
		{
			$pemesanan = PemesananList::find($request->Id[$key]);
			$pemesanan->QRetur = $request->QRetur[$key];
			$pemesanan->ReturCode = $request->ReturCode;
			$pemesanan->save();
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Retur on Code '.$request['ReturCode'];
		$history->save();
		
		return redirect()->route('retur.show', $retur->id)->with('message', 'Retur with Code '.$retur->ReturCode.' is  created');
  }

  public function show($id){
    $retur = Retur::select('retur.*', 'pemesanan.id as idPesan', 'pemesanan.PesanCode')
		->leftJoin('pemesanan', 'retur.PesanCode', 'pemesanan.PesanCode')
		->where('retur.id', $id)
		->first();
    $pemesananlists = PemesananList::select('pemesananlist.QRetur', 'pemesananlist.ICode', 'inventory.*')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesananlist.ReturCode', $retur->ReturCode)
    ->get();
    
    if(in_array("show", $this->access)){
      return view('pages.retur.show')
      ->with('url', 'retur')
      ->with('retur', $retur)
      ->with('pemesananlists', $pemesananlists)
      ->with('top_menu_sel', 'menu_retur')
      ->with('page_title', 'Retur')
      ->with('page_description', 'Show');
    }else
      return redirect()->back();
  }

  public function edit($id){
    $retur = Retur::select('retur.*', 'pemesanan.PesanCode')
		->leftJoin('pemesanan', 'retur.PesanCode', 'pemesanan.PesanCode')
		->where('retur.id', $id)
		->first();
    $pemesananlists = PemesananList::select('pemesananlist.QRetur', 'pemesananlist.ICode', 'inventory.*')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesananlist.ReturCode', $retur->ReturCode)
    ->get();

    if(in_array("edit", $this->access)){
      return view('pages.retur.edit')
      ->with('url', 'retur')
      ->with('retur', $retur)
      ->with('pemesananlists', $pemesananlists)
      ->with('top_menu_sel', 'menu_retur')
      ->with('page_title', 'retur')
      ->with('page_description', 'Edit');
    }else
      return redirect()->back();
  }

  public function update(Request $request, $id){
		//Validation
		$this->validate($request, [
			'ReturCode'=>'required|unique:retur,ReturCode,'.$request->OldRetur.',ReturCode',
			'Tgl'=>'required',
			'Transport'=>'required'
		], [
			'ReturCode.required' => 'The Retur Code field is required.',
			'ReturCode.unique' => 'The Retur Code has already been taken.',
			'Tgl.required' => 'The Date field is required.',
			'Transport.required' => 'The Transport field is required.'
		]);
    
		$retur = Retur::find($id);
		$retur->ReturCode = $request->ReturCode;
		$retur->Tgl = $request->Tgl;
		$retur->Transport = str_replace(".","",substr($request->Transport, 3));
		$retur->PesanCode = $request->PesanCode;
		$retur->save();
		
		$pemesanans = $request->Id;
		foreach ($pemesanans as $key => $pemesanan)
		{
			$pemesanan = PemesananList::find($request->Id[$key]);
			$pemesanan->QRetur = $request->QRetur[$key];
			$pemesanan->ReturCode = $request->ReturCode;
			$pemesanan->save();
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update Retur on Code '.$request['Retur'];
    $history->save();

    return redirect()->route('retur.show', $id);
  }

  public function DeleteRetur(Request $request){
    Retur::where('ReturCode', $request->ReturCode)->delete();
		DB::statement('ALTER TABLE retur auto_increment = 1;');
		
		$pemesanans = PemesananList::where('ReturCode', $request->ReturCode)
		->get();
		foreach ($pemesanans as $pemesanan)
		{
			$pemesanan = PemesananList::find($pemesanan->id);
			$pemesanan->QRetur = null;
			$pemesanan->ReturCode = null;
			$pemesanan->save();
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Retur on Code '.$request->ReturCode;
    $history->save();
    
    Session::flash('message', 'Retur with Code '.$request->ReturCode.' is deleted');
  }
}
