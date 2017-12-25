<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pemesanan;
use App\PemesananList;
use App\Penerimaan;
use App\Retur;
use App\History;
use Session;
use DB;
use Auth;

class PemesananController extends Controller
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
		$pemesanans = Pemesanan::select([
			'pemesanan.*',
			DB::raw('SUM(pemesananlist.Amount*pemesananlist.QTerima) AS Price'),
			DB::raw('sum(pemesananlist.Quantity) AS SumQuantity'),
			DB::raw('sum(pemesananlist.QTerima) AS SumQTerima'),
			'penerimaan.TerimaCode',
			'penerimaan.Transport',
			'supplier.Company'
		])
		->leftJoin('pemesananlist', 'pemesanan.PesanCode', 'pemesananlist.PesanCode')
		->leftJoin('penerimaan', 'pemesanan.PesanCode', 'penerimaan.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->groupBy('pemesanan.PesanCode')
		->get();
		
		if(in_array("index", $this->access)){
			return view('pages.pemesanan.indexs')
			->with('url', 'pemesanan')
			->with('pemesanans', $pemesanans)
			->with('top_menu_sel', 'menu_pemesanan')
			->with('page_title', 'Pemesanan')
			->with('page_description', 'Index');
		}else
      return redirect()->back();
	}
	
  public function create(){
    if(in_array("create", $this->access)){
      return view('pages.pemesanan.create')
      ->with('url', 'pemesanan')
      ->with('top_menu_sel', 'menu_pemesanan')
      ->with('page_title', 'Pemesanan')
      ->with('page_description', 'Create');
    }else
        return redirect()->back();
  }

  public function store(Request $request){
		//Validation
		$this->validate($request, [
			'PesanCode'=>'required|unique:pemesanan',
			'Tgl'=>'required',
			'SCode'=>'required'
		], [
			'PesanCode.required' => 'The Pesan Code field is required.',
			'PesanCode.unique' => 'The Pesan Code has already been taken.',
			'Tgl.required' => 'The Date field is required.',
			'SCode.required' => 'The Supplier Code field is required.'
		]);
		
		$pemesanan = new Pemesanan;
		$pemesanan->PesanCode = $request->PesanCode;
		$pemesanan->Tgl = $request->Tgl;
		$pemesanan->SCode = $request->SCode;
		$pemesanan->save();
		
		$pemesananlists = $request->Barang;
		foreach ($pemesananlists as $key => $pemesananlist)
		{
			$pemesananlist = new PemesananList;
			$pemesananlist->Quantity = $request->Quantity[$key];
			$pemesananlist->Amount = str_replace(".","",substr($request->Amount[$key], 3));
			$pemesananlist->ICode = $request->ICode[$key];
			$pemesananlist->PesanCode = $request->PesanCode;
			$pemesananlist->save();
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Pemesanan on Code '.$request['Pemesanan'];
		$history->save();
		
		return redirect()->route('pemesanan.show', $pemesanan->id)->with('message', 'Pemesanan with Code '.$pemesanan->PesanCode.' is  created');
  }

  public function show($id){
    $pemesanan = Pemesanan::select('pemesanan.*', 'supplier.*', 'penerimaan.id as idTerima', 'penerimaan.TerimaCode', 'penerimaan.Transport as TransportTerima', 'penerimaan.Tgl as TglTerima', 'retur.id as idRetur', 'retur.ReturCode', 'retur.Transport as TransportRetur', 'retur.Tgl as TglRetur')
		->leftJoin('penerimaan', 'pemesanan.PesanCode', 'penerimaan.PesanCode')
		->leftJoin('retur', 'pemesanan.PesanCode', 'retur.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->where('pemesanan.id', $id)
		->first();
    $pemesananlists = PemesananList::select('pemesananlist.*', 'pemesanan.*', 'inventory.Barang', 'inventory.Type')
		->leftJoin('pemesanan', 'pemesananlist.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesanan.id', $id)
    ->orderBy('pemesanan.id', 'asc')
    ->get();
		$terimacheck = Penerimaan::leftJoin('pemesanan', 'penerimaan.PesanCode', 'pemesanan.PesanCode')
		->where('pemesanan.id', $id)
		->first();
		
		$returcheck = Retur::leftJoin('pemesanan', 'retur.PesanCode', 'pemesanan.PesanCode')
		->where('pemesanan.id', $id)
		->first();
		$qreturcheck = PemesananList::select([
			DB::raw('sum(pemesananlist.Quantity) AS SumQuantity'),
			DB::raw('sum(pemesananlist.QTerima) AS SumQTerima')
		])
		->where('PesanCode', $pemesanan->PesanCode)
		->groupBy('PesanCode')
		->first();
    
    if(in_array("show", $this->access)){
      return view('pages.pemesanan.show')
      ->with('url', 'pemesanan')
      ->with('pemesanan', $pemesanan)
      ->with('pemesananlists', $pemesananlists)
      ->with('terimacheck', $terimacheck)
      ->with('returcheck', $returcheck)
      ->with('qreturcheck', $qreturcheck)
      ->with('top_menu_sel', 'menu_pemesanan')
      ->with('page_title', 'Pemesanan')
      ->with('page_description', 'Show');
    }else
      return redirect()->back();
  }

  public function edit($id){
    $pemesanan = Pemesanan::find($id);
    $pemesananlists = PemesananList::select('pemesananlist.*', 'pemesanan.*', 'inventory.Barang', 'inventory.Type')
		->leftJoin('pemesanan', 'pemesananlist.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesanan.id', $id)
    ->orderBy('pemesanan.id', 'asc')
    ->get();

    if(in_array("edit", $this->access)){
      return view('pages.pemesanan.edit')
      ->with('url', 'pemesanan')
      ->with('pemesanan', $pemesanan)
      ->with('pemesananlists', $pemesananlists)
      ->with('top_menu_sel', 'menu_pemesanan')
      ->with('page_title', 'Pemesanan')
      ->with('page_description', 'Edit');
    }else
      return redirect()->back();
  }

  public function update(Request $request, $id){
		//Validation
		$this->validate($request, [
			'PesanCode'=>'required|unique:pemesanan,PesanCode,'.$request->OldPemesanan.',PesanCode',
			'Tgl'=>'required',
			'SCode'=>'required'
		], [
			'PesanCode.required' => 'The Pesan Code field is required.',
			'PesanCode.unique' => 'The Pesan Code has already been taken.',
			'Tgl.required' => 'The Date field is required.',
			'SCode.required' => 'The Supplier Code field is required.'
		]);
    
		$pemesanan = Pemesanan::find($id);
		$pemesanan->PesanCode = $request->PesanCode;
		$pemesanan->Tgl = $request->Tgl;
		$pemesanan->SCode = $request->SCode;
		$pemesanan->save();

    PemesananList::where('PesanCode', $pemesanan->PesanCode)->delete();
		DB::statement('ALTER TABLE pemesananlist auto_increment = 1;');
		
    $pemesananlists = $request->Barang;
    foreach ($pemesananlists as $key => $pemesananlist)
		{
			$pemesananlist = new PemesananList;
			$pemesananlist->Quantity = $request->Quantity[$key];
			$pemesananlist->Amount = str_replace(".","",substr($request->Amount[$key], 3));
			$pemesananlist->ICode = $request->ICode[$key];
			$pemesananlist->PesanCode = $request->PesanCode;
			$pemesananlist->save();
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update Pemesanan on Code '.$request['Pemesanan'];
    $history->save();

    return redirect()->route('pemesanan.show', $id);
  }

  public function DeletePemesanan(Request $request){
    Pemesanan::where('PesanCode', $request->PesanCode)->delete();
		DB::statement('ALTER TABLE pemesanan auto_increment = 1;');
    PemesananList::where('PesanCode', $request->PesanCode)->delete();
		DB::statement('ALTER TABLE pemesananlist auto_increment = 1;');
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Pemesanan on Code '.$request->PesanCode;
    $history->save();
    
    Session::flash('message', 'Pesan with Code '.$request->PesanCode.' is deleted');
  }
}
