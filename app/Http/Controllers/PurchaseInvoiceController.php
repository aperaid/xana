<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PurchaseInvoice;
use App\Penerimaan;
use App\Retur;
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
			'supplier.Company'
		])
		->leftJoin('pemesanan', 'purchaseinvoice.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('supplier', 'pemesanan.SCode', 'supplier.SCode')
		->where('purchaseinvoice.id', $id)
		->first();
		
    $purchaseinvoices = PurchaseInvoice::select([
			'purchaseinvoice.*',
			'pemesananlist.*',
			DB::raw('(pemesananlist.QTTerima-pemesananlist.QTRetur)*pemesananlist.Amount AS Total'),
			'inventory.*'
		])
		->leftJoin('pemesanan', 'purchaseinvoice.PesanCode', 'pemesanan.PesanCode')
		->leftJoin('pemesananlist', 'pemesanan.PesanCode', 'pemesananlist.PesanCode')
		->leftJoin('inventory', 'pemesananlist.ICode', 'inventory.Code')
		->get();
		
		$penerimaans = Penerimaan::select('penerimaan.*')
		->leftJoin('purchaseinvoice', 'penerimaan.PesanCode', 'purchaseinvoice.PesanCode')
		->where('purchaseinvoice.id', $id)
		->get();
		
		$returs = Retur::select('retur.*')
		->leftJoin('purchaseinvoice', 'retur.PesanCode', 'purchaseinvoice.PesanCode')
		->where('purchaseinvoice.id', $id)
		->get();
		
		$Total = array_sum($purchaseinvoices->pluck('Total')->toArray());
		$TransportTerima = array_sum($penerimaans->pluck('Transport')->toArray());
		$TransportRetur = array_sum($returs->pluck('Transport')->toArray());
		$GrandTotal = $Total+$TransportTerima-$purchaseinvoice->Discount-$purchaseinvoice->Pembulatan-$TransportRetur;
		
		$tglterima = str_replace('/', '-', $purchaseinvoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$purchaseinvoice->Termin." days"));
		
    if(in_array("edit", $this->access)){
      return view('pages.purchaseinvoice.edit')
      ->with('url', 'purchaseinvoice')
      ->with('purchaseinvoice', $purchaseinvoice)
      ->with('purchaseinvoices', $purchaseinvoices)
      ->with('Total', $Total)
      ->with('TransportTerima', $TransportTerima)
      ->with('TransportRetur', $TransportRetur)
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
	
	function terbilang($x, $style=4){
    if($x<0) {
      $hasil = "minus ". trim(kekata($x));
    } else {
      $hasil = ucwords(trim($this->kekata($x)));
    }     
    switch ($style) {
      case 1:
        $hasil = strtoupper($hasil);
        break;
      case 2:
        $hasil = strtolower($hasil);
        break;
      case 3:
        $hasil = ucwords($hasil);
        break;
      default:
        $hasil = ucfirst($hasil);
        break;
    }
    return $hasil;
  }
	
	public function getInvoice($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    
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
    
    $document = $phpWord->loadTemplate(public_path('/template/PurchaseInvoice.docx'));
    
    $document->setValue('Company', ''.$purchaseinvoice->Company.'');
    $document->setValue('CompAlamat', ''.$purchaseinvoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$purchaseinvoice->CompPhone.'');
    $document->setValue('PCode', ''.$purchaseinvoice->PCode.'');
    $document->setValue('Project', ''.$purchaseinvoice->Project.'');
    $document->setValue('Invoice', ''.$purchaseinvoice->Invoice.'');
    $document->setValue('Tgl', ''.$transaksis->first()->Tgl.'');
    $document->setValue('POCode', ''.$pocode->POCode.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$purchaseinvoice->podisc.'');
    $document->setValue('Discount', ''.number_format($Discount, 0, ',','.').'');
    $document->setValue('Transport', ''.number_format($Transport, 0, ',','.').'');
    $document->setValue('PPN', ''.number_format($Pajak, 0, ',','.').'');
    $document->setValue('Totals', ''.number_format($GrandTotal, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($GrandTotal)).' Rupiah'.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Type'.$key, ''.$transaksi->Type.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      $document->setValue('Quantity'.$key, ''.$transaksi->QTertanda.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($transaksi->Amount, 0, ',', '.').'');
      $document->setValue('Total'.$key, ''.number_format($total2[$key], 0, ',', '.').'');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Type'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
      $document->setValue('Price'.$x, '');
      $document->setValue('Total'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
    if($invoice->PPN==1)
			$path = sprintf("C:\Users\Public\Documents\PPN\JUAL\INV\INV_", $user);
		else
			$path = sprintf("C:\Users\Public\Documents\NON PPN\JUAL\INV\INV_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('INV_%s.docx', $clear);
    
    //save as a random file in temp file
		$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
		$document->saveAs($temp_file);
		
		// Your browser will name the file "myFile.docx"
		// regardless of what it's named on the server 
		header("Content-Disposition: attachment; filename=$download");
		//readfile($temp_file); // or 
		echo file_get_contents($temp_file);
		unlink($temp_file);  // remove temp file
		
		//Session::flash('message', 'Downloaded to Server Public Documents file name INV_'.$download);
    //return redirect()->route('invoice.showjual', $id);
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
