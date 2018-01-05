<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pemesanan;
use App\PemesananList;
use App\PurchaseInvoice;
use App\Penerimaan;
use App\Supplier;
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
			'Tgl'=>'required',
			'SCode'=>'required'
		], [
			'Tgl.required' => 'The Date field is required.',
			'SCode.required' => 'The Supplier Code field is required.'
		]);
		
		$date = explode('/', $request->Tgl);
		$count = Supplier::where('SCode', $request->SCode)->first();
		
		$pesancode = 'PESAN/'.$request->SCode.'/'.$date[2].$date[1].$date[0].'/'.str_pad($count->Count, 5, '0', STR_PAD_LEFT);
		$purchaseinvoicecode = 'INVOICE/'.$request->SCode.'/'.$date[2].$date[1].$date[0].'/'.str_pad($count->Count, 5, '0', STR_PAD_LEFT);
		
		$count->Count = $count->Count + 1;
		$count->save();
		
		$pemesanan = new Pemesanan;
		$pemesanan->PesanCode = $pesancode;
		$pemesanan->Tgl = $request->Tgl;
		$pemesanan->SCode = $request->SCode;
		$pemesanan->save();
		
		$purchaseinvoice = new PurchaseInvoice;
		$purchaseinvoice->PurchaseInvoice = $purchaseinvoicecode;
		$purchaseinvoice->Tgl = $request->Tgl;
		$purchaseinvoice->PesanCode = $pesancode;
		$purchaseinvoice->save();
		
		$pemesananlists = $request->Barang;
		foreach ($pemesananlists as $key => $pemesananlist)
		{
			$pemesananlist = new PemesananList;
			$pemesananlist->Quantity = $request->Quantity[$key];
			$pemesananlist->Amount = str_replace(".","",substr($request->Amount[$key], 3));
			$pemesananlist->ICode = $request->ICode[$key];
			$pemesananlist->PesanCode = $pesancode;
			$pemesananlist->save();
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Pemesanan on Code '.$request['Pemesanan'];
		$history->save();
		
		return redirect()->route('pemesanan.show', $pemesanan->id)->with('message', 'Pemesanan with Code '.$pesancode.' is  created');
  }

  public function show($id){
    $pemesanan = Pemesanan::select('pemesanan.id as idPesan', 'pemesanan.*', 'supplier.*', 'penerimaan.id as idTerima', 'penerimaan.TerimaCode', 'penerimaan.Transport as TransportTerima', 'penerimaan.Tgl as TglTerima', 'retur.id as idRetur', 'retur.ReturCode', 'retur.Transport as TransportRetur', 'retur.Tgl as TglRetur', 'purchaseinvoice.id as idInvoice', 'purchaseinvoice.PurchaseInvoice', 'purchaseinvoice.Tgl as TglInvoice', 'purchaseinvoice.Discount', 'purchaseinvoice.Catatan', 'purchaseinvoice.TglTerima', 'purchaseinvoice.Termin', 'purchaseinvoice.Pembulatan')
		->leftJoin('penerimaan', 'pemesanan.PesanCode', 'penerimaan.PesanCode')
		->leftJoin('retur', 'pemesanan.PesanCode', 'retur.PesanCode')
		->leftJoin('purchaseinvoice', 'pemesanan.PesanCode', 'purchaseinvoice.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->where('pemesanan.id', $id)
		->first();
    $pemesananlists = PemesananList::select(['pemesananlist.*', DB::raw('pemesananlist.Amount*pemesananlist.QTerima AS Total'), 'pemesanan.*', 'inventory.Barang', 'inventory.Type'])
		->leftJoin('pemesanan', 'pemesananlist.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesanan.id', $id)
    ->orderBy('pemesanan.id', 'asc')
    ->get();
		
		$Total = array_sum($pemesananlists->pluck('Total')->toArray());
		$GrandTotal = $Total+$pemesanan->TransportTerima-$pemesanan->Discount-$pemesanan->Pembulatan-$pemesanan->TransportRetur;
		
		$tglterima = str_replace('/', '-', $pemesanan->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$pemesanan->Termin." days"));
		
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
      ->with('Total', $Total)
      ->with('GrandTotal', $GrandTotal)
      ->with('duedate', $duedate)
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
			'Tgl'=>'required',
			'SCode'=>'required'
		], [
			'Tgl.required' => 'The Date field is required.',
			'SCode.required' => 'The Supplier Code field is required.'
		]);
    
		$pemesanan = Pemesanan::find($id);
		$pemesanan->Tgl = $request->Tgl;
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
