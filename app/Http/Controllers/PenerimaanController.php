<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pemesanan;
use App\PemesananList;
use App\Penerimaan;
use App\PenerimaanList;
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
		$pemesanans = Pemesanan::select('pemesanan.id as idPesan', 'pemesanan.PesanCode', 'pemesananlist.*', 'inventory.Barang', 'inventory.Type')
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
		
		$last_id = Penerimaan::max('id') + 1;
		
		$terimacode = 'TERIMA/'.$pesancode[1].'/'.$date[2].$date[1].$date[0].'/'.str_pad($last_id, 5, '0', STR_PAD_LEFT);
		
		$penerimaan = new Penerimaan;
		$penerimaan->TerimaCode = $terimacode;
		$penerimaan->Tgl = $request->Tgl;
		$penerimaan->Transport = str_replace(".","",substr($request->Transport, 3));
		$penerimaan->PesanCode = $request->PesanCode;
		$penerimaan->save();
		
		$pemesananlists = $request->Id;
		foreach ($pemesananlists as $key => $pemesananlist)
		{
			$pemesananlist = PemesananList::find($request->Id[$key]);
			$pemesananlist->QTTerima = $pemesananlist->QTTerima+$request->QTerima[$key];
			$pemesananlist->save();
		}
		
		$penerimaanlists = $request->Id;
		foreach ($penerimaanlists as $key => $penerimaanlist)
		{
			if($request->QTerima[$key]!=0){
				$penerimaanlist = new PenerimaanList;
				$penerimaanlist->QTerima = $request->QTerima[$key];
				$penerimaanlist->TerimaCode = $terimacode;
				$penerimaanlist->idPesanList = $request->Id[$key];
				$penerimaanlist->save();
			}
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
    $pemesananlists = PemesananList::select('pemesananlist.id', 'pemesananlist.ICode', 'penerimaanlist.QTerima', 'inventory.Barang', 'inventory.Type')
		->leftJoin('penerimaanlist', 'pemesananlist.id', 'penerimaanlist.idPesanList')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('penerimaanlist.TerimaCode', $penerimaan->TerimaCode)
    ->get();
		
		$returcheck = Retur::where('Retur.PesanCode', $penerimaan->PesanCode)
		->first();
    
    if(in_array("show", $this->access)){
      return view('pages.penerimaan.show')
      ->with('url', 'penerimaan')
      ->with('penerimaan', $penerimaan)
      ->with('pemesananlists', $pemesananlists)
      ->with('returcheck', $returcheck)
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
    $pemesananlists = PemesananList::select('pemesananlist.Quantity', 'pemesananlist.QTTerima', 'pemesananlist.ICode', 'penerimaanlist.id', 'penerimaanlist.QTerima', 'inventory.Barang', 'inventory.Type')
		->leftJoin('penerimaanlist', 'pemesananlist.id', 'penerimaanlist.idPesanList')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('penerimaanlist.TerimaCode', $penerimaan->TerimaCode)
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
		
		$penerimaanlists = $request->Id;
		foreach ($penerimaanlists as $key => $penerimaanlist)
		{
			$penerimaanlist = PenerimaanList::find($request->Id[$key]);
			$pemesananlist = PemesananList::where('id', $penerimaanlist->idPesanList)->first();
			$pemesananlist->QTTerima = $pemesananlist->QTTerima-$penerimaanlist->QTerima+$request->QTerima[$key];
			$penerimaanlist->QTerima = $request->QTerima[$key];
			$penerimaanlist->save();
			$pemesananlist->save();
			
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update Penerimaan on Code '.$request['Penerimaan'];
    $history->save();

    return redirect()->route('penerimaan.show', $id);
  }

  public function DeletePenerimaan(Request $request){
		$pemesananlists = PemesananList::select('pemesananlist.*', 'penerimaanlist.QTerima')
		->leftJoin('penerimaanlist', 'pemesananlist.id', 'penerimaanlist.idPesanList')
		->where('TerimaCode', $request->TerimaCode)
		->get();
		
		foreach ($pemesananlists as $pemesananlist)
		{
			$pemesananlist->QTTerima = $pemesananlist->QTTerima-$pemesananlist->QTerima;
			$pemesananlist->save();
		}
		
		Penerimaan::where('TerimaCode', $request->TerimaCode)->delete();
		DB::statement('ALTER TABLE penerimaan auto_increment = 1;');
		PenerimaanList::where('TerimaCode', $request->TerimaCode)->delete();
		DB::statement('ALTER TABLE penerimaanlist auto_increment = 1;');
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Penerimaan on Code '.$request->TerimaCode;
    $history->save();
    
    Session::flash('message', 'Terima with Code '.$request->TerimaCode.' is deleted');
  }
}
