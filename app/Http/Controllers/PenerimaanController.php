<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
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

class PenerimaanController extends Controller
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
		$penerimaans = Penerimaan::select('penerimaan.*', 'supplier.Company')
		->leftJoin('pemesanan', 'penerimaan.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->get();
		
		if(in_array("index", $this->access)){
			return view('pages.penerimaan.indexs')
			->with('url', 'penerimaan')
			->with('penerimaans', $penerimaans)
			->with('top_menu_sel', 'menu_penerimaan')
			->with('page_title', 'Penerimaan')
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
      return view('pages.penerimaan.create')
      ->with('url', 'penerimaan')
			->with('pemesanans', $pemesanans)
      ->with('top_menu_sel', 'menu_penerimaan')
      ->with('page_title', 'Penerimaan')
      ->with('page_description', 'Create');
    }else
        return redirect()->back();
  }

  public function store(Request $request){
		//Validation
		$this->validate($request, [
			'Tgl'=>'required',
			'PesanCode'=>'required'
		], [
			'Tgl.required' => 'The Date field is required.',
			'PesanCode.required' => 'The Pesan Code field is required.'
		]);
		
		$date = explode('/', $request->Tgl);
		$pesancode = explode('/', $request->PesanCode);
		
		$terimacode = 'TERIMA/'.$pesancode[1].'/'.$date[2].$date[1].$date[0].'/'.$pesancode[3];
		
		$penerimaan = new Penerimaan;
		$penerimaan->TerimaCode = $terimacode;
		$penerimaan->Tgl = $request->Tgl;
		$penerimaan->Transport = str_replace(".","",substr($request->Transport, 3));
		$penerimaan->PesanCode = $request->PesanCode;
		$penerimaan->save();
		
		$pemesanans = $request->Id;
		foreach ($pemesanans as $key => $pemesanan)
		{
			$pemesanan = PemesananList::find($request->Id[$key]);
			$pemesanan->QTerima = $request->QTerima[$key];
			$pemesanan->TerimaCode = $terimacode;
			$pemesanan->save();
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Penerimaan on Code '.$terimacode;
		$history->save();
		
		return redirect()->route('penerimaan.show', $penerimaan->id)->with('message', 'Penerimaan with Code '.$terimacode.' is  created');
  }

  public function show($id){
    $penerimaan = Penerimaan::select('penerimaan.*', 'pemesanan.id as idPesan', 'pemesanan.PesanCode')
		->leftJoin('pemesanan', 'penerimaan.PesanCode', 'pemesanan.PesanCode')
		->where('penerimaan.id', $id)
		->first();
    $pemesananlists = PemesananList::select('pemesananlist.id', 'pemesananlist.QTerima', 'pemesananlist.ICode', 'inventory.Barang', 'inventory.Type')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesananlist.TerimaCode', $penerimaan->TerimaCode)
    ->get();
		
		$returcheck = Retur::leftJoin('pemesanan', 'retur.PesanCode', 'pemesanan.PesanCode')
		->where('pemesanan.PesanCode', $penerimaan->PesanCode)
		->first();
		$qreturcheck = PemesananList::select([
			DB::raw('sum(pemesananlist.Quantity) AS SumQuantity'),
			DB::raw('sum(pemesananlist.QTerima) AS SumQTerima')
		])
		->where('PesanCode', $penerimaan->PesanCode)
		->groupBy('PesanCode')
		->first();
    
    if(in_array("show", $this->access)){
      return view('pages.penerimaan.show')
      ->with('url', 'penerimaan')
      ->with('penerimaan', $penerimaan)
      ->with('pemesananlists', $pemesananlists)
      ->with('returcheck', $returcheck)
      ->with('qreturcheck', $qreturcheck)
      ->with('top_menu_sel', 'menu_penerimaan')
      ->with('page_title', 'Penerimaan')
      ->with('page_description', 'Show');
    }else
      return redirect()->back();
  }

  public function edit($id){
    $penerimaan = Penerimaan::select('penerimaan.*', 'pemesanan.PesanCode')
		->leftJoin('pemesanan', 'penerimaan.PesanCode', 'pemesanan.PesanCode')
		->where('penerimaan.id', $id)
		->first();
    $pemesananlists = PemesananList::select('pemesananlist.id', 'pemesananlist.Quantity', 'pemesananlist.QTerima', 'pemesananlist.ICode', 'inventory.Barang', 'inventory.Type')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesananlist.TerimaCode', $penerimaan->TerimaCode)
    ->get();

    if(in_array("edit", $this->access)){
      return view('pages.penerimaan.edit')
      ->with('url', 'penerimaan')
      ->with('penerimaan', $penerimaan)
      ->with('pemesananlists', $pemesananlists)
      ->with('top_menu_sel', 'menu_penerimaan')
      ->with('page_title', 'Penerimaan')
      ->with('page_description', 'Edit');
    }else
      return redirect()->back();
  }

  public function update(Request $request, $id){
		//Validation
		$this->validate($request, [
			'Tgl'=>'required',
			'Transport'=>'required'
		], [
			'Tgl.required' => 'The Date field is required.',
			'Transport.required' => 'The Transport field is required.'
		]);
    
		$penerimaan = Penerimaan::find($id);
		$penerimaan->Tgl = $request->Tgl;
		$penerimaan->Transport = str_replace(".","",substr($request->Transport, 3));
		$penerimaan->PesanCode = $request->PesanCode;
		$penerimaan->save();
		
		$pemesanans = $request->Id;
		foreach ($pemesanans as $key => $pemesanan)
		{
			$pemesanan = PemesananList::find($request->Id[$key]);
			$pemesanan->QTerima = $request->QTerima[$key];
			$pemesanan->save();
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update Penerimaan on Code '.$request['Penerimaan'];
    $history->save();

    return redirect()->route('penerimaan.show', $id);
  }

  public function DeletePenerimaan(Request $request){
    Penerimaan::where('TerimaCode', $request->TerimaCode)->delete();
		DB::statement('ALTER TABLE penerimaan auto_increment = 1;');
		
		$pemesanans = PemesananList::where('TerimaCode', $request->TerimaCode)
		->get();
		foreach ($pemesanans as $pemesanan)
		{
			$pemesanan = PemesananList::find($pemesanan->id);
			$pemesanan->QTerima = null;
			$pemesanan->TerimaCode = null;
			$pemesanan->save();
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Penerimaan on Code '.$request->TerimaCode;
    $history->save();
    
    Session::flash('message', 'Terima with Code '.$request->TerimaCode.' is deleted');
  }
}
