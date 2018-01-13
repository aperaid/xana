<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pemesanan;
use App\PemesananList;
use App\PurchaseInvoice;
use App\Penerimaan;
use App\PenerimaanList;
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
			'pemesanan.id',
			'pemesanan.PesanCode',
			'pemesanan.Tgl',
			DB::raw('SUM(pemesananlist.Amount*pemesananlist.Quantity) AS Price'),
			DB::raw('sum(pemesananlist.Quantity) AS SumQuantity'),
			DB::raw('sum(pemesananlist.QTTerima) AS SumQTTerima'),
			'purchaseinvoice.TglTerima',
			'purchaseinvoice.Lunas',
			'supplier.Company'
		])
		->leftJoin('pemesananlist', 'pemesanan.PesanCode', 'pemesananlist.PesanCode')
		->leftJoin('purchaseinvoice', 'pemesanan.PesanCode', 'purchaseinvoice.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->groupBy('pemesanan.PesanCode')
		->get();
		
		$transports = Pemesanan::select([DB::raw('SUM(penerimaan.Transport) as SumTransport')])
		->leftJoin('penerimaan', 'pemesanan.PesanCode', 'penerimaan.PesanCode')
		->groupBy('pemesanan.PesanCode')
		->pluck('SumTransport')
		->toArray();
		
		if(in_array("index", $this->access)){
			return view('pages.pemesanan.indexs')
			->with('url', 'pemesanan')
			->with('pemesanans', $pemesanans)
			->with('transports', $transports)
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
    $pemesanan = Pemesanan::select(['pemesanan.id as idPesan', 'pemesanan.*', 'supplier.*', 'purchaseinvoice.id as idInvoice', 'purchaseinvoice.*'])
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->leftJoin('purchaseinvoice', 'pemesanan.PesanCode', 'purchaseinvoice.PesanCode')
		->where('pemesanan.id', $id)
		->first();
    $pemesananlists = PemesananList::select(['pemesananlist.*', DB::raw('(pemesananlist.QTTerima-pemesananlist.QTRetur)*pemesananlist.Amount AS Total'), 'inventory.Barang', 'inventory.Type'])
		->leftJoin('pemesanan', 'pemesananlist.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->where('pemesanan.id', $id)
    ->orderBy('pemesanan.id', 'asc')
    ->get();
		
		$penerimaans = Penerimaan::select('penerimaan.*')
		->leftJoin('pemesanan', 'penerimaan.PesanCode', 'pemesanan.PesanCode')
		->where('pemesanan.id', $id)
		->get();
		
		$returs = Retur::select('retur.*')
		->leftJoin('pemesanan', 'retur.PesanCode', 'pemesanan.PesanCode')
		->where('pemesanan.id', $id)
		->get();
		
		$Total = array_sum($pemesananlists->pluck('Total')->toArray());
		$TransportTerima = array_sum($penerimaans->pluck('Transport')->toArray());
		$TransportRetur = array_sum($returs->pluck('Transport')->toArray());
		$GrandTotal = $Total+$TransportTerima-$pemesanan->Discount-$pemesanan->Pembulatan-$TransportRetur;
		
		$tglterima = str_replace('/', '-', $pemesanan->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$pemesanan->Termin." days"));
    
    if(in_array("show", $this->access)){
      return view('pages.pemesanan.show')
      ->with('url', 'pemesanan')
      ->with('pemesanan', $pemesanan)
      ->with('pemesananlists', $pemesananlists)
			->with('penerimaans', $penerimaans)
			->with('returs', $returs)
      ->with('Total', $Total)
      ->with('TransportTerima', $TransportTerima)
      ->with('TransportRetur', $TransportRetur)
      ->with('GrandTotal', $GrandTotal)
      ->with('duedate', $duedate)
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
		$SCode = Pemesanan::where('PesanCode', $request->PesanCode)->first()->SCode;
		$count = Supplier::where('SCode', $SCode)->first();
		$count->Count = $count->Count-1;
		$count->save();
		
    Pemesanan::where('PesanCode', $request->PesanCode)->delete();
		DB::statement('ALTER TABLE pemesanan auto_increment = 1;');
    PemesananList::where('PesanCode', $request->PesanCode)->delete();
		DB::statement('ALTER TABLE pemesananlist auto_increment = 1;');
    PurchaseInvoice::where('PesanCode', $request->PesanCode)->delete();
		DB::statement('ALTER TABLE purchaseinvoice auto_increment = 1;');
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete Pemesanan on Code '.$request->PesanCode;
    $history->save();
    
    Session::flash('message', 'Pesan with Code '.$request->PesanCode.' is deleted');
  }
}
