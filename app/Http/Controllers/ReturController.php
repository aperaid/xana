<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pemesanan;
use App\PemesananList;
use App\PenerimaanList;
use App\Retur;
use App\ReturList;
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
		$pemesanans = Pemesanan::select('pemesanan.id as idPesan', 'pemesanan.PesanCode', 'pemesananlist.*', 'inventory.Barang', 'inventory.Type')
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
			'Tgl'=>'required',
			'PesanCode'=>'required'
		], [
			'Tgl.required' => 'The Date field is required.',
			'PesanCode.required' => 'The Pesan Code field is required.'
		]);
		
		$date = explode('/', $request->Tgl);
		$pesancode = explode('/', $request->PesanCode);
		
		$last_id = Retur::max('id') + 1;
		
		$returcode = 'RETUR/'.$pesancode[1].'/'.$date[2].$date[1].$date[0].'/'.str_pad($last_id, 5, '0', STR_PAD_LEFT);
		
		$retur = new Retur;
		$retur->ReturCode = $returcode;
		$retur->Tgl = $request->Tgl;
		$retur->Transport = str_replace(".","",substr($request->Transport, 3));
		$retur->PesanCode = $request->PesanCode;
		$retur->save();
		
		$pemesananlists = $request->Id;
		foreach ($pemesananlists as $key => $pemesananlist)
		{
			$pemesananlist = PemesananList::find($request->Id[$key]);
			$pemesananlist->QTRetur = $pemesananlist->QTRetur+$request->QRetur[$key];
			$pemesananlist->save();
		}
		
		$returlists = $request->Id;
		foreach ($returlists as $key => $returlist)
		{
			if($request->QRetur[$key]!=0){
				$returlist = new ReturList;
				$returlist->QRetur = $request->QRetur[$key];
				$returlist->ReturCode = $returcode;
				$returlist->idPesanList = $request->Id[$key];
				$returlist->save();
			}
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Retur on Code '.$returcode;
		$history->save();
		
		return redirect()->route('retur.show', $retur->id)->with('message', 'Retur with Code '.$returcode.' is  created');
  }

  public function show($id){
    $retur = Retur::select('retur.*', 'pemesanan.id as idPesan', 'pemesanan.PesanCode')
		->leftJoin('pemesanan', 'retur.PesanCode', 'pemesanan.PesanCode')
		->where('retur.id', $id)
		->first();
    $pemesananlists = PemesananList::select('pemesananlist.id', 'pemesananlist.ICode', 'returlist.QRetur', 'inventory.Barang', 'inventory.Type')
		->leftJoin('returlist', 'pemesananlist.id', 'returlist.idPesanList')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('returlist.ReturCode', $retur->ReturCode)
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
    $pemesananlists = PemesananList::select('pemesananlist.Quantity', 'pemesananlist.QTRetur', 'pemesananlist.ICode', 'returlist.id', 'returlist.QRetur', 'inventory.Barang', 'inventory.Type')
		->leftJoin('returlist', 'pemesananlist.id', 'returlist.idPesanList')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('returlist.ReturCode', $retur->ReturCode)
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
			'Tgl'=>'required',
			'Transport'=>'required'
		], [
			'Tgl.required' => 'The Date field is required.',
			'Transport.required' => 'The Transport field is required.'
		]);
    
		$retur = Retur::find($id);
		$retur->Tgl = $request->Tgl;
		$retur->Transport = str_replace(".","",substr($request->Transport, 3));
		$retur->PesanCode = $request->PesanCode;
		$retur->save();
		
		$returlists = $request->Id;
		foreach ($returlists as $key => $returlist)
		{
			$returlist = ReturList::find($request->Id[$key]);
			$pemesananlist = PemesananList::where('id', $returlist->idPesanList)->first();
			$pemesananlist->QTRetur = $pemesananlist->QTRetur-$returlist->QRetur+$request->QRetur[$key];
			$returlist->QRetur = $request->QRetur[$key];
			$returlist->save();
			$pemesananlist->save();
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update Retur on Code '.$request['Retur'];
    $history->save();

    return redirect()->route('retur.show', $id);
  }

  public function DeleteRetur(Request $request){
		$pemesananlists = PemesananList::select('pemesananlist.*', 'returlist.QRetur')
		->leftJoin('returlist', 'pemesananlist.id', 'returlist.idPesanList')
		->where('ReturCode', $request->ReturCode)
		->get();
		
		foreach ($pemesananlists as $pemesananlist)
		{
			$pemesananlist->QTRetur = $pemesananlist->QTRetur-$pemesananlist->QRetur;
			$pemesananlist->save();
		}
		
    Retur::where('ReturCode', $request->ReturCode)->delete();
		DB::statement('ALTER TABLE retur auto_increment = 1;');
		ReturList::where('ReturCode', $request->ReturCode)->delete();
		DB::statement('ALTER TABLE returlist auto_increment = 1;');
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Retur on Code '.$request->ReturCode;
    $history->save();
    
    Session::flash('message', 'Retur with Code '.$request->ReturCode.' is deleted');
  }
}
