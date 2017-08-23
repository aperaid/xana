<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoicePisah;
use App\Periode;
use App\Transaksi;
use App\SJKirim;
use App\IsiSJKirim;
use App\TransaksiClaim;
use App\TransaksiExchange;
use App\PO;
use App\History;
use Session;
use DB;
use Auth;

class InvoiceController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Administrator'||Auth::user()->access=='PPNAdmin'||Auth::user()->access=='NonPPNAdmin'||Auth::user()->access=='Purchasing'||Auth::user()->access=='SuperPurchasing'))
				$this->access = array("showsewa", "showsewapisah", "showjual", "showjualpisah", "showclaim", "index");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
  public function getInvoiceSewa($id){
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.id as pocusid',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.Project',
      'customer.Company',
			'customer.PPN',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $periodes = Periode::select([
      'sjkirim.SJKir',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
			DB::raw('SUM(periode.Quantity) as SumQuantity'),
      'periode.S',
      'periode.E',
      'periode.SJKem',
      'periode.Deletes',
      'periode.Periode',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    //->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $sjkemcheck = 0;
    $kemexist = Transaksi::where('transaksi.Reference', $invoice -> Reference)
    ->count();
    $kemfound = Transaksi::selectRaw('SUM(QSisaKem) as kemfound')
    ->where('transaksi.Reference', $invoice -> Reference)
    ->first();
    if($kemexist == 0){
      $sjkemcheck = 0;
    }else{
      if($kemfound -> kemfound == 0){
				$sjkemcheck = 0;
			}else{
				$sjkemcheck = 1;
			}
		}

    $total = 0;
    foreach($periodes as $key => $periode){
      $start = $periode->S;
      $end = $periode->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2[] = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $I[] = round(((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key], 4);
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode->SumQuantity*$periode->Amount; 
      $total += $total2[$key];
    }

		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
		
		$Discount = $total*$invoice->podisc/100;

		$Transport = $toss*($invoice->TimesKembali+$invoice->Times);

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*($invoice->TimesKembali+$invoice->Times);
		
		$GrandTotal = $total+$Transport+$Pajak-$Discount-$invoice->Discount-$invoice->Pembulatan;
		
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
    
    if(in_array("showsewa", $this->access)){
      return view('pages.invoice.showsewa')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('periodes', $periodes)
      ->with('pocode', $pocode)
      ->with('sjkemcheck', $sjkemcheck)
      ->with('SE', $SE)
      ->with('Days2', $Days2)
      ->with('I', $I)
      ->with('toss', $toss)
      ->with('total', $total)
      ->with('GrandTotal', $GrandTotal)
      ->with('Discount', $Discount)
      ->with('Pajak', $Pajak)
      ->with('Transport', $Transport)
			->with('GrandTotalTransport', $GrandTotalTransport)
			->with('duedate', $duedate)
      ->with('total2', $total2)
      ->with('top_menu_sel', 'menu_invoice')
      ->with('page_title', 'Invoice Sewa')
      ->with('page_description', 'View');
    }else
      return redirect()->back();
	}
	
	public function getInvoiceSewaPisah($id){
    $invoice = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'pocustomer.id as pocusid',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $invoice->Invoice)
    ->first();
    
    $periodes = Periode::select([
      'sjkirim.SJKir',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
			DB::raw('SUM(periode.Quantity) as SumQuantity'),
      'periode.S',
      'periode.E',
      'periode.SJKem',
      'periode.Deletes',
      'periode.Periode',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
		->where('transaksi.POCode', $invoice->POCode)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    //->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $sjkemcheck = 0;
    $kemexist = Transaksi::where('transaksi.Reference', $invoice -> Reference)
    ->count();
    $kemfound = Transaksi::selectRaw('SUM(QSisaKem) as kemfound')
    ->where('transaksi.Reference', $invoice -> Reference)
    ->first();
    if($kemexist == 0){
      $sjkemcheck = 0;
    }else{
      if($kemfound -> kemfound == 0){
				$sjkemcheck = 0;
			}else{
				$sjkemcheck = 1;
			}
		}

    $total = 0;
    foreach($periodes as $key => $periode){
      $start = $periode->S;
      $end = $periode->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2[] = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $I[] = round(((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key], 4);
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode->SumQuantity*$periode->Amount;
      $total += $total2[$key];
    }

		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}

    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*($invoice->TimesKembali+$invoice->Times);

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*($invoice->TimesKembali+$invoice->Times);
		
		$GrandTotal = $total+$Transport+$Pajak-$Discount-$invoice->Discount-$invoice->Pembulatan;
		
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
    
    if(in_array("showsewapisah", $this->access)){
      return view('pages.invoice.showsewapisah')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('periodes', $periodes)
      ->with('sjkemcheck', $sjkemcheck)
			->with('SE', $SE)
      ->with('Days2', $Days2)
      ->with('I', $I)
      ->with('toss', $toss)
      ->with('total', $total)
      ->with('GrandTotal', $GrandTotal)
      ->with('Discount', $Discount)
      ->with('Pajak', $Pajak)
      ->with('Transport', $Transport)
			->with('GrandTotalTransport', $GrandTotalTransport)
			->with('duedate', $duedate)
			->with('total2', $total2)
      ->with('top_menu_sel', 'menu_invoice')
      ->with('page_title', 'Invoice Sewa Pisah')
      ->with('page_description', 'View');
    }else
      return redirect()->back();
	}
	
  public function postInvoiceSewa(Request $request, $id){
    $invoice = Invoice::find($id);
		$invoice->Times = $request->Times;
		$invoice->TimesKembali = $request->TimesKembali;
    $invoice->Discount = str_replace(".","",substr($request->Discount, 3));
		$invoice->TglTerima = $request->TglTerima;
		$invoice->Termin = $request->Termin;
    $invoice->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
    $invoice->Catatan = $request->Catatan;
    $invoice->save();
		
		$invoicepisahs = InvoicePisah::where('Reference', $invoice->Reference)
		->where('Periode', $invoice->Periode)
		->where('JSC', 'Sewa')
		->get();
    
		$invoicepisahs = $invoicepisahs->pluck('id');
		foreach ($invoicepisahs as $key => $invoicepisah)
    {
			$invoicepisah = InvoicePisah::find($invoicepisahs[$key]);
			$invoicepisah->Times = 0;
			$invoicepisah->TimesKembali = 0;
			$invoicepisah->TglTerima = $invoice->TglTerima;
			$invoicepisah->Termin = $invoice->Termin;
			//$invoicepisah->Discount = str_replace(".","",substr($invoice->Discount, 3));
			//$invoicepisah->Pembulatan = str_replace(".","",substr($invoice->Pembulatan, 3));
			$invoicepisah->save();
		}
		InvoicePisah::where('id', $invoicepisahs)->update(['Times' => $invoice->Times, 'TimesKembali' => $invoice->TimesKembali]);

    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update invoice on Invoice '.$request['Invoice'];
    $history->save();

    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('invoice.showsewa', $id);
  }
	
	public function postInvoiceSewaPisah(Request $request, $id){
    $invoicepisah = InvoicePisah::find($id);
		$invoicepisah->Times = $request->Times;
		$invoicepisah->TimesKembali = $request->TimesKembali;
    $invoicepisah->Discount = str_replace(".","",substr($request->Discount, 3));
		$invoicepisah->TglTerima = $request->TglTerima;
		$invoicepisah->Termin = $request->Termin;
    $invoicepisah->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
    $invoicepisah->Catatan = $request->Catatan;
    $invoicepisah->save();
		
		$invoice = Invoice::where('Reference', $invoicepisah->Reference)
		->where('Periode', $invoicepisah->Periode)
		->where('JSC', 'Sewa')
		->first();
		
		$totaltimes = InvoicePisah::where('Reference', $invoicepisah->Reference)
		->where('Periode', $invoicepisah->Periode)
		->where('JSC', 'Sewa')
		->get();
    
    $invoice = Invoice::find($invoice->id);
		$invoice->Times = $totaltimes->sum('Times');
		$invoice->TimesKembali = $totaltimes->sum('TimesKembali');
		$invoice->TglTerima = $invoicepisah->TglTerima;
		$invoice->Termin = $invoicepisah->Termin;
    //$invoicepisah->Discount = str_replace(".","",substr($request->Discount, 3));
    //$invoicepisah->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
		$invoice->save();
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update invoice on Invoice Pisah '.$request['Invoice'];
    $history->save();

    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('invoice.showsewapisah', $id);
  }
  
  public function getBAS($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) as SumQuantity'),
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    //->where('periode.Quantity', '!=' , 0)
		->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $firststart = $periodes->pluck('S');
    
    foreach($periodes as $key => $periode){
      $start = $periode->S;
      $end = $periode->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;
    }
    $Quantity = $periodes->sum('SumQuantity');
    
    $document = $phpWord->loadTemplate(public_path('/template/BAS.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('S', ''.$firststart[0].'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('Quantity', ''.$Quantity.'');
    $document->setValue('PEO', ''.$pocode->POCode.'');

    foreach ($periodes as $key => $periodes)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$periodes->Barang.'');
      $document->setValue('S'.$key, ''.$periodes->S.'');
      $document->setValue('E'.$key, ''.$periodes->E.'');
      $document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('Quantity'.$key, ''.$periodes->SumQuantity.'');
      $document->setValue('Sat'.$key, 'PCS');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
		if($invoice->PPN==1)
			$path = sprintf("C:\Users\Public\Documents\PPN\SEWA\BA\BAS_", $user);
		else
			$path = sprintf("C:\Users\Public\Documents\NON PPN\SEWA\BA\BAS_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name BA_'.$download);
    return redirect()->route('invoice.showsewa', $id);
  }
	
	public function getBAPisah($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $invoice->Invoice)
    ->first();
		
    $periodes = Periode::select([
      'transaksi.Barang',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) as SumQuantity'),
      'transaksi.POCode'
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
		->where('po.POCode', $invoice->POCode)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    //->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $firststart = $periodes->pluck('S');
    
    foreach($periodes as $key => $periode){
      $start = $periode->S;
      $end = $periode->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;
    }
    $Quantity = $periodes->sum('SumQuantity');
    
    $document = $phpWord->loadTemplate(public_path('/template/BA.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('S', ''.$firststart[0].'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('Quantity', ''.$Quantity.'');
    $document->setValue('PEO', ''.$periodes->first()->POCode.'');

    foreach ($periodes as $key => $periodes)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$periodes->Barang.'');
      $document->setValue('S'.$key, ''.$periodes->S.'');
      $document->setValue('E'.$key, ''.$periodes->E.'');
      $document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('Quantity'.$key, ''.$periodes->SumQuantity.'');
      $document->setValue('Sat'.$key, 'PCS');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
    if($invoice->PPN==1)
			$path = sprintf("C:\Users\Public\Documents\PPN\SEWA\BA\BA_", $user);
		else
			$path = sprintf("C:\Users\Public\Documents\NON PPN\SEWA\BA\BA_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name BA_'.$download);
    return redirect()->route('invoice.showsewapisah', $id);
  }
  
  function kekata($x){
    $x = abs($x);
    $angka = array("", "satu", "dua", "tiga", "empat", "lima",
    "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if($x <12) {
      $temp = " ". $angka[$x];
    }else if ($x <20) {
      $temp = $this->kekata($x - 10). " belas";
    }else if ($x <100) {
      $temp = $this->kekata($x/10)." puluh". $this->kekata($x % 10);
    }else if ($x <200) {
      $temp = " seratus" . $this->kekata($x - 100);
    }else if ($x <1000) {
      $temp = $this->kekata($x/100) . " ratus" . $this->kekata($x % 100);
    }else if ($x <2000) {
      $temp = " seribu" . $this->kekata($x - 1000);
    }else if ($x <1000000) {
      $temp = $this->kekata($x/1000) . " ribu" . $this->kekata($x % 1000);
    }else if ($x <1000000000) {
      $temp = $this->kekata($x/1000000) . " juta" . $this->kekata($x % 1000000);
    }else if ($x <1000000000000) {
      $temp = $this->kekata($x/1000000000) . " milyar" . $this->kekata(fmod($x,1000000000));
    }else if ($x <1000000000000000) {
      $temp = $this->kekata($x/1000000000000) . " trilyun" . $this->kekata(fmod($x,1000000000000));
    }
    return $temp;
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
  
  public function getInvs($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'transaksi.Amount',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) as SumQuantity')
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    //->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $total = 0;
    foreach($periodes as $key => $periode){
      $start = $periode->S;
      $end = $periode->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $duedate = date('d/m/Y', strtotime($end2."+4 days"));
      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2[] = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $I[] = round(((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key], 4);
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode->SumQuantity*$periode->Amount;
      $total += $total2[$key];
    }

		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*($invoice->TimesKembali+$invoice->Times);

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*($invoice->TimesKembali+$invoice->Times);
		
		$GrandTotal = $total+$Transport+$Pajak-$Discount;
    
    if($invoice->PPN==1)
      $document = $phpWord->loadTemplate(public_path('/template/Invsp.docx'));
    else
      $document = $phpWord->loadTemplate(public_path('/template/Invsnp.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('S', ''.$periodes->first()->S.'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('DueDate', ''.$duedate.'');
    $document->setValue('POCode', ''.$pocode->POCode.'');
    $document->setValue('Quantity', ''.$periodes->sum('SumQuantity').'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$invoice->podisc.'');
    $document->setValue('Discount', ''.number_format($Discount, 0, ',','.').'');
    $document->setValue('Transport', ''.number_format($Transport, 0, ',','.').'');
    $document->setValue('PPN', ''.number_format($Pajak, 0, ',','.').'');
    $document->setValue('Totals', ''.number_format($GrandTotal, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($GrandTotal)).' Rupiah'.'');

    foreach ($periodes as $key => $periode)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$periode->Barang.'');
      $document->setValue('S'.$key, ''.$periode->S.'');
      $document->setValue('E'.$key, ''.$periode->E.'');
      $document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('I'.$key, ''.$I[$key].'');
      $document->setValue('Quantity'.$key, ''.$periode->SumQuantity.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($periode->Amount, 0, ',', '.').'');
      $document->setValue('Total'.$key, ''.number_format($total2[$key], 0, ',', '.').'');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('I'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
      $document->setValue('Price'.$x, '');
      $document->setValue('Total'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
		if($invoice->PPN==1)
			$path = sprintf("C:\Users\Public\Documents\PPN\SEWA\INV\INV_", $user);
		else
			$path = sprintf("C:\Users\Public\Documents\NON PPN\SEWA\INV\INV_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INV_'.$download);
    return redirect()->route('invoice.showsewa', $id);
  }
	
	public function getInvsPisah($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $invoice->Invoice)
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'transaksi.Amount',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) as SumQuantity'),
      'transaksi.POCode',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
		->where('po.POCode', $invoice->POCode)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    //->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $total = 0;
    foreach($periodes as $key => $periode){
      $start = $periode->S;
      $end = $periode->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $duedate = date('d/m/Y', strtotime($end2."+4 days"));
      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2[] = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $I[] = round(((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key], 4);
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode->SumQuantity*$periode->Amount;
      $total += $total2[$key];
    }

		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*($invoice->TimesKembali+$invoice->Times);

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*($invoice->TimesKembali+$invoice->Times);
		
		$GrandTotal = $total+$Transport+$Pajak-$Discount;
    
    if($invoice->PPN==1)
      $document = $phpWord->loadTemplate(public_path('/template/Invsp.docx'));
    else
      $document = $phpWord->loadTemplate(public_path('/template/Invsnp.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('S', ''.$periodes->first()->S.'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('DueDate', ''.$duedate.'');
    $document->setValue('POCode', ''.$periodes->first()->POCode.'');
    $document->setValue('Quantity', ''.$periodes->sum('SumQuantity').'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$invoice->podisc.'');
    $document->setValue('Discount', ''.number_format($Discount, 0, ',','.').'');
    $document->setValue('Transport', ''.number_format($Transport, 0, ',','.').'');
    $document->setValue('PPN', ''.number_format($Pajak, 0, ',','.').'');
    $document->setValue('Totals', ''.number_format($GrandTotal, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($GrandTotal)).' Rupiah'.'');

    foreach ($periodes as $key => $periode)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$periode->Barang.'');
      $document->setValue('S'.$key, ''.$periode->S.'');
      $document->setValue('E'.$key, ''.$periode->E.'');
      $document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('I'.$key, ''.$I[$key].'');
      $document->setValue('Quantity'.$key, ''.$periode->SumQuantity.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($periode->Amount, 0, ',', '.').'');
      $document->setValue('Total'.$key, ''.number_format($total2[$key], 0, ',', '.').'');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('I'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
      $document->setValue('Price'.$x, '');
      $document->setValue('Total'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
    if($invoice->PPN==1)
			$path = sprintf("C:\Users\Public\Documents\PPN\SEWA\INV\INV_", $user);
		else
			$path = sprintf("C:\Users\Public\Documents\NON PPN\SEWA\INV\INV_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INV_'.$download);
    return redirect()->route('invoice.showsewapisah', $id);
  }
  
  public function getInvst($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'transaksi.Amount',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) as SumQuantity'),
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    //->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $total = 0;
    foreach($periodes as $key => $periode){
      $start = $periode->S;
      $end = $periode->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $duedate = date('d/m/Y', strtotime($end2."+4 days"));
      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2[] = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $I[] = round(((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key], 4);
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode->SumQuantity*$periode->Amount;
      //$total += $total2[$key];
    }
    
		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
		
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*($invoice->TimesKembali+$invoice->Times);

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*($invoice->TimesKembali+$invoice->Times);
		
		$GrandTotal = $total+$GrandTotalTransport+$Pajak-$Discount;
    
    $document = $phpWord->loadTemplate(public_path('/template/Invst.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('S', ''.$periodes->first()->S.'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('DueDate', ''.$duedate.'');
    $document->setValue('POCode', ''.$pocode->POCode.'');
    $document->setValue('Quantity', ''.$periodes->sum('SumQuantity').'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$invoice->podisc.'');
    $document->setValue('Discount', ''.number_format($Discount, 0, ',','.').'');
    $document->setValue('Transport', ''.number_format($GrandTotalTransport, 0, ',','.').'');
    $document->setValue('PPN', ''.number_format($Pajak, 0, ',','.').'');
    $document->setValue('Totals', ''.number_format($GrandTotal, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($GrandTotal)).' Rupiah'.'');

    foreach ($periodes as $key => $periode)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$periode->Barang.'');
      $document->setValue('S'.$key, ''.$periode->S.'');
      $document->setValue('E'.$key, ''.$periode->E.'');
      $document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('I'.$key, ''.$I[$key].'');
      $document->setValue('Quantity'.$key, ''.'0'.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($periode->Amount, 0, ',', '.').'');
      $document->setValue('Total'.$key, ''.'0'.'');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('I'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
      $document->setValue('Price'.$x, '');
      $document->setValue('Total'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
		$path = sprintf("C:\Users\Public\Documents\PPN\SEWA\INV\INVT_");
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INVT_'.$download);
    return redirect()->route('invoice.showsewa', $id);
  }
	
	public function getInvstPisah($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $invoice->Invoice)
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'transaksi.Amount',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) as SumQuantity'),
      'transaksi.POCode',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
		->where('po.POCode', $invoice->POCode)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    //->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $total = 0;
    foreach($periodes as $key => $periode){
      $start = $periode->S;
      $end = $periode->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $duedate = date('d/m/Y', strtotime($end2."+4 days"));
      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2[] = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $I[] = round(((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key], 4);
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode->SumQuantity*$periode->Amount;
      //$total += $total2[$key];
    }
    
    if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*($invoice->TimesKembali+$invoice->Times);

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*($invoice->TimesKembali+$invoice->Times);
		
		$GrandTotal = $total+$GrandTotalTransport+$Pajak-$Discount;
    
    $document = $phpWord->loadTemplate(public_path('/template/Invst.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('S', ''.$periodes->first()->S.'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('DueDate', ''.$duedate.'');
    $document->setValue('POCode', ''.$periodes->first()->POCode.'');
    $document->setValue('Quantity', ''.$periodes->sum('SumQuantity').'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$invoice->podisc.'');
    $document->setValue('Discount', ''.number_format($Discount, 0, ',','.').'');
    $document->setValue('Transport', ''.number_format($GrandTotalTransport, 0, ',','.').'');
    $document->setValue('PPN', ''.number_format($Pajak, 0, ',','.').'');
    $document->setValue('Totals', ''.number_format($GrandTotal, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($GrandTotal)).' Rupiah'.'');

    foreach ($periodes as $key => $periode)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$periode->Barang.'');
      $document->setValue('S'.$key, ''.$periode->S.'');
      $document->setValue('E'.$key, ''.$periode->E.'');
      $document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('I'.$key, ''.$I[$key].'');
      $document->setValue('Quantity'.$key, ''.'0'.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($periode->Amount, 0, ',', '.').'');
      $document->setValue('Total'.$key, ''.'0'.'');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('I'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
      $document->setValue('Price'.$x, '');
      $document->setValue('Total'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
		$path = sprintf("C:\Users\Public\Documents\PPN\SEWA\INV\INVT_");
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INVT_'.$download);
    return redirect()->route('invoice.showsewapisah', $id);
  }

  public function getInvoiceJual($id){
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $transaksis = Transaksi::select([
			'inventory.Type',
      'isisjkirim.QTertanda',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
    ])
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
		//->where('isisjkirim.QTertanda', '!=' , 0)
    ->orderBy('isisjkirim.id', 'asc')
    ->get();
    
    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QTertanda*$transaksi->Amount;
      $total += $total2[$x];
      $x++;
    }
		
		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*$invoice->Times;

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*$invoice->Times;
		
		$GrandTotal = $total+$Transport+$Pajak-$Discount-$invoice->Discount-$invoice->Pembulatan;
		
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
		
    if(in_array("showjual", $this->access)){
      return view('pages.invoice.showjual')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('transaksis', $transaksis)
      ->with('pocode', $pocode)
      ->with('total', $total)
      ->with('GrandTotal', $GrandTotal)
      ->with('Discount', $Discount)
      ->with('Transport', $Transport)
			->with('GrandTotalTransport', $GrandTotalTransport)
			->with('duedate', $duedate)
      ->with('Pajak', $Pajak)
      ->with('total2', $total2)
      ->with('top_menu_sel', 'menu_invoice')
      ->with('page_title', 'Invoice Jual')
      ->with('page_description', 'View');
    }else
      return redirect()->back();
	}
	
	public function getInvoiceJualPisah($id){
    $invoice = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $invoice->Invoice)
    ->first();
    
    $transaksis = Transaksi::select([
			'inventory.Type',
      'isisjkirim.QTertanda',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
			'transaksi.POCode',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
    ])
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
		->where('transaksi.POCode', $invoice->POCode)
    ->where('transaksi.JS', 'Jual')
		//->where('isisjkirim.QTertanda', '!=' , 0)
    ->orderBy('isisjkirim.id', 'asc')
    ->get();
    
    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QTertanda*$transaksi->Amount;
      $total += $total2[$x];
      $x++;
    }
		
		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*$invoice->Times;

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*$invoice->Times;
		
		$GrandTotal = $total+$Transport+$Pajak-$Discount-$invoice->Discount-$invoice->Pembulatan;
		
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
		
    if(in_array("showjualpisah", $this->access)){
      return view('pages.invoice.showjualpisah')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('transaksis', $transaksis)
      ->with('total', $total)
      ->with('GrandTotal', $GrandTotal)
      ->with('Discount', $Discount)
      ->with('Transport', $Transport)
			->with('GrandTotalTransport', $GrandTotalTransport)
			->with('duedate', $duedate)
      ->with('Pajak', $Pajak)
      ->with('total2', $total2)
      ->with('top_menu_sel', 'menu_invoice')
      ->with('page_title', 'Invoice Jual Pisah')
      ->with('page_description', 'View');
    }else
      return redirect()->back();
	}
	
  public function postInvoiceJual(Request $request, $id){
    $invoice = Invoice::find($id);
		$invoice->Times = $request->Times;
    $invoice->Discount = str_replace(".","",substr($request->Discount, 3));
		$invoice->TglTerima = $request->TglTerima;
		$invoice->Termin = $request->Termin;
    $invoice->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
    $invoice->Catatan = $request->Catatan;
    $invoice->save();
		
		$invoicepisahs = InvoicePisah::where('Reference', $invoice->Reference)
		->where('JSC', 'Jual')
		->get();
    
		$invoicepisahs = $invoicepisahs->pluck('id');
		foreach ($invoicepisahs as $key => $invoicepisah)
    {
			$invoicepisah = InvoicePisah::find($invoicepisahs[$key]);
			$invoicepisah->Times = 0;
			$invoicepisah->TglTerima = $invoice->TglTerima;
			$invoicepisah->Termin = $invoice->Termin;
			$invoicepisah->save();
		}
		InvoicePisah::where('id', $invoicepisahs)->update(['Times' => $invoice->Times]);

    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update invoice on Invoice '.$request['Invoice'];
    $history->save();
    
    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('invoice.showjual', $id);
  }
	
	public function postInvoiceJualPisah(Request $request, $id){
    $invoicepisah = InvoicePisah::find($id);
		$invoicepisah->Times = $request->Times;
    $invoicepisah->Discount = str_replace(".","",substr($request->Discount, 3));
		$invoicepisah->TglTerima = $request->TglTerima;
		$invoicepisah->Termin = $request->Termin;
    $invoicepisah->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
    $invoicepisah->Catatan = $request->Catatan;
    $invoicepisah->save();
		
		$invoice = Invoice::where('Reference', $invoicepisah->Reference)
		->where('JSC', 'Jual')
		->first();
		
		$totaltimes = InvoicePisah::where('Reference', $invoicepisah->Reference)
		->where('Periode', $invoicepisah->Periode)
		->where('JSC', 'Jual')
		->get();
    
    $invoice = Invoice::find($invoice->id);
		$invoice->Times = $totaltimes->sum('Times');
		$invoice->TglTerima = $invoicepisah->TglTerima;
		$invoice->Termin = $invoicepisah->Termin;
		$invoice->save();

    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update invoice on Invoice Pisah '.$request['Invoice'];
    $history->save();
    
    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('invoice.showjualpisah', $id);
  }
	
	public function getBAJ($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $transaksis = Transaksi::select([
			'inventory.Type',
      'isisjkirim.QTertanda',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
    ])
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
		//->where('isisjkirim.QTertanda', '!=' , 0)
    ->orderBy('isisjkirim.id', 'asc')
    ->get();

    $Quantity = $transaksis->sum('QTertanda');
    
    $document = $phpWord->loadTemplate(public_path('/template/BAJ.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$transaksis->first()->Tgl.'');
    $document->setValue('Quantity', ''.$Quantity.'');
    $document->setValue('PEO', ''.$pocode->POCode.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      $document->setValue('Quantity'.$key, ''.$transaksi->QTertanda.'');
      $document->setValue('Sat'.$key, 'PCS');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
		if($invoice->PPN==1)
			$path = sprintf("C:\Users\Public\Documents\PPN\JUAL\BA\BAJ_", $user);
		else
			$path = sprintf("C:\Users\Public\Documents\NON PPN\JUAL\BA\BAJ_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name BAJ_'.$download);
    return redirect()->route('invoice.showjual', $id);
  }
  
  public function getInvj($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $transaksis = Transaksi::select([
      'inventory.Type',
      'isisjkirim.QTertanda',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'transaksi.*',
    ])
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
		//->where('isisjkirim.QTertanda', '!=' , 0)
    ->orderBy('isisjkirim.id', 'asc')
    ->get();

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QTertanda*$transaksi->Amount;
      $total += $total2[$x];
      $x++;
    }
    
		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*$invoice->Times;

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*$invoice->Times;
		
		$GrandTotal = $total+$Transport+$Pajak-$Discount;
    
    if($invoice->PPN==1)
      $document = $phpWord->loadTemplate(public_path('/template/Invjp.docx'));
    else
      $document = $phpWord->loadTemplate(public_path('/template/Invjnp.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$transaksis->first()->Tgl.'');
    $document->setValue('POCode', ''.$pocode->POCode.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$invoice->podisc.'');
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
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INV_'.$download);
    return redirect()->route('invoice.showjual', $id);
  }
	
	public function getInvjPisah($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $invoice->Invoice)
    ->first();
    
    $transaksis = Transaksi::select([
      'inventory.Type',
      'isisjkirim.QTertanda',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'transaksi.*',
    ])
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
		->where('transaksi.POCode', $invoice->POCode)
    ->where('transaksi.JS', 'Jual')
		//->where('isisjkirim.QTertanda', '!=' , 0)
    ->orderBy('isisjkirim.id', 'asc')
    ->get();

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QTertanda*$transaksi->Amount;
      $total += $total2[$x];
      $x++;
    }
    
		if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*$invoice->Times;

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*$invoice->Times;
		
		$GrandTotal = $total+$Transport+$Pajak-$Discount;
    
    if($invoice->PPN==1)
      $document = $phpWord->loadTemplate(public_path('/template/Invjp.docx'));
    else
      $document = $phpWord->loadTemplate(public_path('/template/Invjnp.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$transaksis->first()->Tgl.'');
    $document->setValue('POCode', ''.$transaksis->first()->POCode.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$invoice->podisc.'');
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
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INV_'.$download);
    return redirect()->route('invoice.showjualpisah', $id);
  }
  
  public function getInvjt($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $transaksis = Transaksi::select([
      'inventory.Type',
      'isisjkirim.QTertanda',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'transaksi.*',
    ])
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
		//->where('isisjkirim.QTertanda', '!=' , 0)
    ->orderBy('isisjkirim.id', 'asc')
    ->get();

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QTertanda*$transaksi->Amount;
      //$total += $total2[$x];
      $x++;
    }
    
    if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*$invoice->Times;

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*$invoice->Times;
		
		$GrandTotal = $total+$GrandTotalTransport+$Pajak-$Discount;
		
    $document = $phpWord->loadTemplate(public_path('/template/Invjt.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$transaksis->first()->Tgl.'');
    $document->setValue('POCode', ''.$pocode->POCode.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$invoice->podisc.'');
    $document->setValue('Discount', ''.number_format($Discount, 0, ',','.').'');
    $document->setValue('Transport', ''.number_format($GrandTotalTransport, 0, ',','.').'');
    $document->setValue('PPN', ''.number_format($Pajak, 0, ',','.').'');
    $document->setValue('Totals', ''.number_format($GrandTotal, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($GrandTotal)).' Rupiah'.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Type'.$key, ''.$transaksi->Type.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      $document->setValue('Quantity'.$key, ''.'0'.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($transaksi->Amount, 0, ',', '.').'');
      $document->setValue('Total'.$key, ''.'0'.'');
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
    $path = sprintf("C:\Users\Public\Documents\PPN\JUAL\INV\INVT_");
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INVT_'.$download);
    return redirect()->route('invoice.showjual', $id);
  }
	
	public function getInvjtPisah($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'pocustomer.Transport',
      'pocustomer.Discount as podisc',
      'pocustomer.PPNT',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $invoice->Invoice)
    ->first();
    
    $transaksis = Transaksi::select([
      'inventory.Type',
      'isisjkirim.QTertanda',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'transaksi.*',
    ])
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
		->where('transaksi.POCode', $invoice->POCode)
    ->where('transaksi.JS', 'Jual')
		//->where('isisjkirim.QTertanda', '!=' , 0)
    ->orderBy('isisjkirim.id', 'asc')
    ->get();

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QTertanda*$transaksi->Amount; 
      //$total += $total2[$x];
      $x++;
    }
    
    if($invoice->PPN == 1 && $invoice->PPNT==0){
			$toss = 0;
			$toss2 = $invoice->Transport;
		}else{
			$toss = $invoice->Transport;
			$toss2 = 0;
		}
    
    $Discount = $total*$invoice->podisc/100;

		$Transport = $toss*$invoice->Times;

    if($invoice->PPN == 1 && $invoice->PPNT==0)
      $Pajak = ($total-$Discount+$Transport)*$invoice->PPN*0.1;
    else
      $Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$GrandTotalTransport = $toss2*$invoice->Times;
		
		$GrandTotal = $total+$GrandTotalTransport+$Pajak-$Discount;
		
    $document = $phpWord->loadTemplate(public_path('/template/Invjt.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$transaksis->first()->Tgl.'');
    $document->setValue('POCode', ''.$transaksis->first()->POCode.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Disc', ''.$invoice->podisc.'');
    $document->setValue('Discount', ''.number_format($Discount, 0, ',','.').'');
    $document->setValue('Transport', ''.number_format($GrandTotalTransport, 0, ',','.').'');
    $document->setValue('PPN', ''.number_format($Pajak, 0, ',','.').'');
    $document->setValue('Totals', ''.number_format($GrandTotal, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($GrandTotal)).' Rupiah'.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Type'.$key, ''.$transaksi->Type.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      $document->setValue('Quantity'.$key, ''.'0'.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($transaksi->Amount, 0, ',', '.').'');
      $document->setValue('Total'.$key, ''.'0'.'');
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
		$path = sprintf("C:\Users\Public\Documents\PPN\JUAL\INV\INVT_");
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INVT_'.$download);
    return redirect()->route('invoice.showjualpisah', $id);
  }
  
  public function getInvoiceClaim($id){
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $transaksis = TransaksiClaim::select([
      'isisjkirim.SJKir',
      'transaksiclaim.*',
      'transaksi.Barang',
      'transaksi.QSisaKem',
    ])
    ->leftJoin('isisjkirim', 'transaksiclaim.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'transaksiclaim.Purchase', '=', 'transaksi.Purchase')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksiclaim.Periode', $invoice->Periode)
    ->groupBy('transaksiclaim.Claim', 'transaksiclaim.Tgl', 'transaksiclaim.Claim')
    ->orderBy('transaksiclaim.id', 'asc')
    ->get();
		
		$exchanges = TransaksiExchange::where('transaksiexchange.Reference', $invoice->Reference)
    ->where('transaksiexchange.Periode', $invoice->Periode)
    ->get();

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QClaim * $transaksi->Amount; 
      $total += $total2[$x];
      $x++;
    }
		
		$extotal = 0;
    $x=0;
    foreach($exchanges as $exchange){
      $extotal2[] = $exchange->QExchange * $exchange->PExchange; 
      $extotal += $extotal2[$x];
      $x++;
    }
		
		$Discount = $total*$transaksis->first()->Discount/100;
		
		$Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$total = $total - $extotal;
		$GrandTotal = $total+$Pajak-$Discount-$invoice->Discount-$invoice->Pembulatan;
    
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
		
    if(in_array("showclaim", $this->access)){
      return view('pages.invoice.showclaim')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('pocode', $pocode)
      ->with('transaksis', $transaksis)
      ->with('exchanges', $exchanges)
      ->with('total', $total)
      ->with('total2', $total2)
      ->with('GrandTotal', $GrandTotal)
      ->with('Discount', $Discount)
			->with('duedate', $duedate)
      ->with('Pajak', $Pajak)
      ->with('top_menu_sel', 'menu_invoice')
      ->with('page_title', 'Invoice Claim')
      ->with('page_description', 'View');
    }else
      return redirect()->back();
	}
  
  public function postInvoiceClaim(Request $request, $id){
		$invoice = Invoice::find($id);
		$invoice->Discount = str_replace(".","",substr($request->Discount, 3));
		$invoice->TglTerima = $request->TglTerima;
		$invoice->Termin = $request->Termin;
		$invoice->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
		$invoice->Catatan = $request->Catatan;
		$invoice->save();

		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Update invoice on Invoice '.$request['Invoice'];
		$history->save();
		
		Session::flash('message', 'Update is successful!');
		
		return redirect()->route('invoice.showclaim', $id);
	}
	
	public function getBAC($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    $transaksis = TransaksiClaim::select([
      'isisjkirim.SJKir',
      'transaksiclaim.*',
      'transaksi.Barang',
      'transaksi.QSisaKem',
    ])
    ->leftJoin('isisjkirim', 'transaksiclaim.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'transaksiclaim.Purchase', '=', 'transaksi.Purchase')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksiclaim.Periode', $invoice->Periode)
    ->groupBy('transaksiclaim.Claim', 'transaksiclaim.Tgl', 'transaksiclaim.Claim')
    ->orderBy('transaksiclaim.id', 'asc')
    ->get();

    $Quantity = $transaksis->sum('QClaim');
		$splittgl = explode('/', $transaksis->first()->Tgl);
		if($splittgl[1]==1)$month = 'Januari';else if($splittgl[1]==2)$month = 'Febuari';else if($splittgl[1]==3)$month = 'Maret';else if($splittgl[1]==4)$month = 'April';else if($splittgl[1]==5)$month = 'Mei';else if($splittgl[1]==6)$month = 'Juni';else if($splittgl[1]==7)$month = 'Juli';else if($splittgl[1]==8)$month = 'Agustus';else if($splittgl[1]==9)$month = 'September';else if($splittgl[1]==10)$month = 'Oktober';else if($splittgl[1]==11)$month = 'November';else if($splittgl[1]==12)$month = 'Desember';
		$Tanggal = $splittgl[0].' '.$month.' '.$splittgl[2];
    
    $document = $phpWord->loadTemplate(public_path('/template/BAC.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$transaksis->first()->Tgl.'');
		$document->setValue('Tanggal', ''.$Tanggal.'');
    $document->setValue('Quantity', ''.$Quantity.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      //$document->setValue('S'.$key, ''.$transaksi->S.'');
      //$document->setValue('E'.$key, ''.$transaksi->E.'');
      //$document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('Quantity'.$key, ''.$transaksi->QClaim.'');
      $document->setValue('Sat'.$key, 'PCS');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
		if($invoice->PPN==1)
			$path = sprintf("C:\Users\Public\Documents\PPN\CLAIM\BA\BAC_", $user);
		else
			$path = sprintf("C:\Users\Public\Documents\NON PPN\CLAIM\BA\BAC_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name BAC_'.$download);
    return redirect()->route('invoice.showclaim', $id);
  }
    
	public function getInvc($id){
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Reference', $invoice->Reference)
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    /*$sjkirim = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('transaksiclaim', 'transaksi.Purchase', '=', 'transaksiclaim.Purchase')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksiclaim.Periode', $invoice->Periode)
    ->where('JS', 'Sewa')
    ->orderBy('isisjkirim.id', 'asc')
    ->first();*/
    
    $pocode = Transaksi::where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('transaksi.POCode')
    ->orderBy('transaksi.id', 'desc')
    ->first();
    
    /*$periode = Periode::where('Reference', $invoice->Reference)
    ->where('Periode', $invoice->Periode)
    ->where('Deletes', 'Kembali')
    ->orderBy('periode.id', 'desc')
    ->first();
    if(isset($periode->SJKem))
      $periode=$periode->SJKem;
    else
      $periode='';*/
    
    $transaksis = TransaksiClaim::select([
      'isisjkirim.SJKir',
      'transaksiclaim.*',
      'periode.S',
      'periode.E',
      'transaksi.Barang',
      'transaksi.QSisaKem',
    ])
    ->leftJoin('isisjkirim', 'transaksiclaim.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'transaksiclaim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('periode', 'isisjkirim.IsiSJKir', '=', 'periode.IsiSJKir')
    ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksiclaim.Periode', $invoice->Periode)
    ->groupBy('transaksiclaim.Claim', 'transaksiclaim.Tgl', 'transaksiclaim.Claim')
    ->orderBy('transaksiclaim.id', 'asc')
    ->get();
		
		$exchanges = TransaksiExchange::where('transaksiexchange.Reference', $invoice->Reference)
    ->where('transaksiexchange.Periode', $invoice->Periode)
    ->get();
		
    $total = 0;
    $x=0;
    foreach($transaksis as $key => $transaksi){
      $start = $transaksi->S;
      $end = $transaksi->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2[] = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $I[] = round(((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key], 4);
      
      $total2[] = $transaksi->QClaim * $transaksi->Amount; 
      $total += $total2[$x];
      $x++;
    }
		
		$extotal = 0;
    $x=0;
    foreach($exchanges as $exchange){
      $extotal2[] = $exchange->QExchange * $exchange->PExchange; 
      $extotal += $extotal2[$x];
      $x++;
    }

    $Discount = $total*$transaksis->first()->Discount/100;
		
		$Pajak = ($total-$Discount)*$invoice->PPN*0.1;
		
		$total = $total - $extotal;
		$GrandTotal = $total+$Pajak-$Discount-$invoice->Discount-$invoice->Pembulatan;
    
    if($invoice->PPN==1)
      $document = $phpWord->loadTemplate(public_path('/template/Invcp.docx'));
    else
      $document = $phpWord->loadTemplate(public_path('/template/Invcnp.docx'));
    
    $tglclaim = str_replace('/', '-', $invoice->Tgl);
    $duedate = date('d/m/Y', strtotime($tglclaim."+4 days"));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('SJKir', ''.$transaksis->first()->SJKir.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$invoice->Tgl.'');
    $document->setValue('DueDate', ''.$duedate.'');
    $document->setValue('POCode', ''.$pocode->POCode.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Discount', ''.number_format($Discount, 0, ',','.').'');
		$document->setValue('Disc', ''.$transaksis->first()->Discount.'');
    $document->setValue('PPN', ''.number_format($Pajak, 0, ',','.').'');
    $document->setValue('Totals', ''.number_format($GrandTotal, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($GrandTotal)).' Rupiah'.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      //$document->setValue('S'.$key, ''.$transaksi->S.'');
      //$document->setValue('E'.$key, ''.$transaksi->E.'');
      //$document->setValue('SE'.$key, ''.$SE[$key].'');
      //$document->setValue('I'.$key, ''.$I[$key].'');
      $document->setValue('Quantity'.$key, ''.$transaksi->QClaim.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($transaksi->Amount, 0, ',', '.').'');
      $document->setValue('Total'.$key, ''.number_format($total2[$key], 0, ',', '.').'');
    }
    
    for($x=0;$x<20;$x++){
      $document->setValue('Key'.$x, '');
      $document->setValue('Barang'.$x, '');
      $document->setValue('S'.$x, '');
      $document->setValue('E'.$x, '');
      $document->setValue('SE'.$x, '');
      $document->setValue('I'.$x, '');
      $document->setValue('Quantity'.$x, '');
      $document->setValue('Sat'.$x, '');
      $document->setValue('Price'.$x, '');
      $document->setValue('Total'.$x, '');
    }
    
    $user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
    if($invoice->PPN==1)
			$path = sprintf("C:\Users\Public\Documents\PPN\CLAIM\INV\INV_", $user);
		else
			$path = sprintf("C:\Users\Public\Documents\NON PPN\CLAIM\INV\INV_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name INV_'.$download);
    return redirect()->route('invoice.showclaim', $id);
  }

	public function index(){
		if(Auth::user()->access == 'Administrator'||Auth::user()->access=='SuperPurchasing'){
			$invoices = Invoice::select([
					'invoice.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->whereRaw('invoice.Reference = periode.Reference')
					->where('periode.Deletes', 'Sewa');
				})
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$invoices = Invoice::select([
					'invoice.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->whereRaw('invoice.Reference = periode.Reference')
					->where('periode.Deletes', 'Sewa');
				})
			->where('customer.PPN', 1)
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$invoices = Invoice::select([
					'invoice.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->whereRaw('invoice.Reference = periode.Reference')
					->where('periode.Deletes', 'Sewa');
				})
			->where('customer.PPN', 0)
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}
		
		if(Auth::user()->access == 'Administrator'||Auth::user()->access=='SuperPurchasing'){
			$invoicesp = InvoicePisah::select([
					'invoicepisah.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicepisah.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 1)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'po.POCode')
					->from('periode')
					->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
					->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
					->whereRaw('invoicepisah.Reference = periode.Reference AND invoicepisah.POCode = po.POCode')
					->where('periode.Deletes', 'Sewa');
				})
			->groupBy('invoicepisah.Reference', 'invoicepisah.POCode', 'invoicepisah.Periode')
			->get();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$invoicesp = InvoicePisah::select([
					'invoicepisah.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicepisah.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 1)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'po.POCode')
					->from('periode')
					->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
					->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
					->whereRaw('invoicepisah.Reference = periode.Reference AND invoicepisah.POCode = po.POCode')
					->where('periode.Deletes', 'Sewa');
				})
			->where('customer.PPN', 1)
			->groupBy('invoicepisah.Reference', 'invoicepisah.POCode', 'invoicepisah.Periode')
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$invoicesp = InvoicePisah::select([
					'invoicepisah.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicepisah.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 1)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'po.POCode')
					->from('periode')
					->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
					->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
					->whereRaw('invoicepisah.Reference = periode.Reference AND invoicepisah.POCode = po.POCode')
					->where('periode.Deletes', 'Sewa');
				})
			->where('customer.PPN', 0)
			->groupBy('invoicepisah.Reference', 'invoicepisah.POCode', 'invoicepisah.Periode')
			->get();
		}
		
		if(Auth::user()->access == 'Administrator'||Auth::user()->access=='SuperPurchasing'){
			$invoicej = Invoice::select([
					'invoice.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			//->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->whereRaw('invoice.Reference = periode.Reference')
					->where('periode.Deletes', 'Jual');
				})
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$invoicej = Invoice::select([
					'invoice.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			//->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->whereRaw('invoice.Reference = periode.Reference')
					->where('periode.Deletes', 'Jual');
				})
			->where('customer.PPN', 1)
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$invoicej = Invoice::select([
					'invoice.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			//->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->whereRaw('invoice.Reference = periode.Reference')
					->where('periode.Deletes', 'Jual');
				})
			->where('customer.PPN', 0)
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}
		
		if(Auth::user()->access == 'Administrator'||Auth::user()->access=='SuperPurchasing'){
			$invoicejp = InvoicePisah::select([
					'invoicepisah.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			->where('pocustomer.INVP', 1)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
					->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
					->whereRaw('invoicepisah.Reference = periode.Reference AND invoicepisah.POCode = po.POCode')
					->where('periode.Deletes', 'Jual');
				})
			->groupBy('invoicepisah.Reference', 'invoicepisah.POCode', 'invoicepisah.Periode')
			->get();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$invoicejp = InvoicePisah::select([
					'invoicepisah.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			->where('pocustomer.INVP', 1)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
					->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
					->whereRaw('invoicepisah.Reference = periode.Reference AND invoicepisah.POCode = po.POCode')
					->where('periode.Deletes', 'Jual');
				})
			->where('customer.PPN', 1)
			->groupBy('invoicepisah.Reference', 'invoicepisah.Periode')
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$invoicejp = InvoicePisah::select([
					'invoicepisah.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
					->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
					->whereRaw('invoicepisah.Reference = periode.Reference AND invoicepisah.POCode = po.POCode')
					->where('periode.Deletes', 'Jual');
				})
			->where('customer.PPN', 0)
			->groupBy('invoicepisah.Reference', 'invoicepisah.Periode')
			->get();
		}
		
		if(Auth::user()->access == 'Administrator'||Auth::user()->access=='SuperPurchasing'){
			$invoicec = Invoice::select([
				'invoice.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Claim')
			->groupBy('invoice.Periode', 'invoice.Reference')
			->get();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$invoicec = Invoice::select([
				'invoice.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Claim')
			->where('customer.PPN', 1)
			->groupBy('invoice.Periode', 'invoice.Reference')
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$invoicec = Invoice::select([
				'invoice.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Claim')
			->where('customer.PPN', 0)
			->groupBy('invoice.Periode', 'invoice.Reference')
			->get();
		}

		if(in_array("index", $this->access)){
			return view('pages.invoice.indexs')
			->with('url', 'invoice')
			->with('invoicess', $invoices)
			->with('invoicesps', $invoicesp)
			->with('invoicejs', $invoicej)
			->with('invoicejps', $invoicejp)
			->with('invoicecs', $invoicec)
			->with('top_menu_sel', 'menu_invoice')
			->with('page_title', 'Invoice')
			->with('page_description', 'Index');
		}else
			return redirect()->back();
	}

	public function postLunas(Request $request){
		if($request->LunasType=='Gabung')
			$invoice = Invoice::find($request->id);
		else if($request->LunasType=='Pisah')
			$invoice = InvoicePisah::find($request->id);

		if($invoice->Lunas == 0)
			$lunas = 1;
		else
			$lunas = 0;
		
		if($request->LunasType=='Gabung')
			Invoice::where('invoice.id', $invoice->id)->update(['invoice.Lunas' => $lunas]);
		else if($request->LunasType=='Pisah')
			InvoicePisah::where('invoicepisah.id', $invoice->id)->update(['invoicepisah.Lunas' => $lunas]);
	}
}
