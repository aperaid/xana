<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PurchaseInvoice;
use App\History;
use Session;
use DB;
use Auth;

class PurchaseInvoiceController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Administrator'||Auth::user()->access=='PPNAdmin'||Auth::user()->access=='NonPPNAdmin'||Auth::user()->access=='Purchasing'||Auth::user()->access=='SuperPurchasing'))
				$this->access = array("showsewa", "showsewapisah", "showjual", "showjualpisah", "edit", "index");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
	public function index(){
		$purchaseinvoices = PurchaseInvoice::select([
				'purchaseinvoice.*',
				'supplier.Company'
			])
		->leftJoin('pemesanan', 'purchaseinvoice.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->get();

		if(in_array("index", $this->access)){
			return view('pages.purchaseinvoice.indexs')
			->with('url', 'purchaseinvoice')
			->with('purchaseinvoices', $purchaseinvoices)
			->with('top_menu_sel', 'menu_purchaseinvoice')
			->with('page_title', 'Purchase Invoice')
			->with('page_description', 'Index');
		}else
			return redirect()->back();
	}
	
  public function edit($id){
    $purchaseinvoice = PurchaseInvoice::select([
			'pemesanan.id as idPesan',
			'purchaseinvoice.*',
			DB::raw('SUM(pemesananlist.Amount*pemesananlist.QTerima) AS Total'),
			'penerimaan.Transport as TransportTerima',
			'retur.Transport as TransportRetur',
			'supplier.Company'
		])
		->leftJoin('pemesanan', 'purchaseinvoice.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('pemesananlist', 'pemesanan.PesanCode', 'pemesananlist.PesanCode')
		->leftJoin('penerimaan', 'pemesanan.PesanCode', 'penerimaan.PesanCode')
		->leftJoin('retur', 'pemesanan.PesanCode', 'retur.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->where('purchaseinvoice.id', $id)
		->groupBy('pemesananlist.PesanCode')
		->first();
		
		$GrandTotal = $purchaseinvoice->Total+$purchaseinvoice->TransportTerima-$purchaseinvoice->Discount-$purchaseinvoice->Pembulatan-$purchaseinvoice->TransportRetur;
		
		$tglterima = str_replace('/', '-', $purchaseinvoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$purchaseinvoice->Termin." days"));
		
    $purchaseinvoices = PurchaseInvoice::select([
			'purchaseinvoice.*',
			'pemesananlist.*',
			'inventory.*'
		])
		->leftJoin('pemesanan', 'purchaseinvoice.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('pemesananlist', 'pemesanan.PesanCode', 'pemesananlist.PesanCode')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->get();
		
    if(in_array("edit", $this->access)){
      return view('pages.purchaseinvoice.edit')
      ->with('url', 'purchaseinvoice')
      ->with('purchaseinvoice', $purchaseinvoice)
      ->with('purchaseinvoices', $purchaseinvoices)
			->with('GrandTotal', $GrandTotal)
			->with('duedate', $duedate)
      ->with('top_menu_sel', 'menu_purchaseinvoice')
      ->with('page_title', 'Purchase Invoice')
      ->with('page_description', 'View');
    }else
      return redirect()->back();
	}
	
  public function update(Request $request, $id){
    $purchaseinvoice = PurchaseInvoice::find($id);
    $purchaseinvoice->Discount = str_replace(".","",substr($request->Discount, 3));
		$purchaseinvoice->TglTerima = $request->TglTerima;
		$purchaseinvoice->Termin = $request->Termin;
    $purchaseinvoice->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
    $purchaseinvoice->Catatan = $request->Catatan;
    $purchaseinvoice->save();

    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update purchaseinvoice on PurchaseInvoice '.$purchaseinvoice->PurchaseInvoice;
    $history->save();
    
    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('purchaseinvoice.edit', $id);
  }

	public function postLunas(Request $request){
		$purchaseinvoice = PurchaseInvoice::find($request->id);
		
		if($purchaseinvoice->Lunas == 0)
			$lunas = 1;
		else
			$lunas = 0;
		
		PurchaseInvoice::where('purchaseinvoice.id', $purchaseinvoice->id)->update(['purchaseinvoice.Lunas' => $lunas]);
	}
}
