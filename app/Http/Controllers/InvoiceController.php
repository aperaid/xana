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
			if(Auth::check()&&(Auth::user()->access == 'Admin'||Auth::user()->access()=='CUSTINVPPN'||Auth::user()->access()=='CUSTINVNONPPN'))
				$this->access = array("showsewa", "showsewapisah", "showjual", "showjualpisah", "showclaim", "index");
			else if(Auth::check()&&(Auth::user()->access()=='POINVPPN'||Auth::user()->access()=='POINVNONPPN'))
				$this->access = array("showsewa", "showsewapisah", "showjual", "showjualpisah", "showclaim", "index");
			else
				$this->access = array("");
			
			if(Auth::user()->access()=='POINVPPN'||Auth::user()->access()=='CUSTINVPPN')
				$this->PPNNONPPN = 1;
			else if(Auth::user()->access()=='POINVNONPPN'||Auth::user()->access()=='CUSTINVNONPPN')
				$this->PPNNONPPN = 0;
    return $next($request);
    });
	}
	
  public function getInvoiceSewa($id){
    $parameter = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.id as pocusid',
      'pocustomer.Transport',
      'pocustomer.PPNT',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Reference', $parameter->Reference)
    ->where('invoice.Invoice', $parameter->Invoice)
    ->first();
    
    $periodes = Periode::select([
      'sjkirim.SJKir',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
      'po.Discount',
			DB::raw('SUM(isisjkirim.QTertanda) as SumQTertanda'),
      'periode.S',
      'periode.E',
      'periode.SJKem',
      'periode.Deletes',
      'periode.Periode',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $parameter->Periode)
    ->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $pocode = PO::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('po.POCode')
    ->orderBy('po.id', 'desc')
    ->first();
    
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
    foreach($periodes as $key => $periode2){
      $start = $periode2->S;
      $end = $periode2->E;

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
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode2->SumQTertanda*($periode2->Amount-($periode2->Amount*$periode2->Discount/100)); 
      $total += $total2[$key];
    }

		if($invoice->PPNT == 1){
			$toss = $invoice->Transport;
			$toss2 = 0;
		}else{
			$toss = 0;
			$toss2 = $invoice->Transport;
		}

    if($invoice->PPNT == 1)
      $totals = number_format((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))-(((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');

    if($invoice->PPNT == 1)
      $Pajak = number_format((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1), 2, ',','.');
    else
      $Pajak = number_format(($total*$invoice->PPN*0.1), 2, ',','.');

		$Transport = number_format(($toss*$invoice->TimesKembali)+($toss*$invoice->Times), 2, ',','.');
		$GrandTotalTransport = number_format(($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times), 2, ',','.');
    
    /*$pocodes = Periode::distinct()
    ->select('transaksi.POCode')
    ->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $parameter->Periode)
    ->where('periode.Quantity', '!=', 0)
    ->groupBy('periode.Purchase', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();*/
		
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
      ->with('totals', $totals)
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
    $parameter = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'pocustomer.id as pocusid',
      'pocustomer.Transport',
      'pocustomer.PPNT',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $parameter->Invoice)
    ->first();
    
    $periodes = Periode::select([
      'sjkirim.SJKir',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
      'po.Discount',
			DB::raw('SUM(isisjkirim.QTertanda) as SumQTertanda'),
      'periode.S',
      'periode.E',
      'periode.SJKem',
      'periode.Deletes',
      'periode.Periode',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $parameter->Reference)
		->where('po.POCode', $parameter->POCode)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $parameter->Periode)
    ->where('periode.Quantity', '!=' , 0)
    ->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $pocode = PO::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
    ->where('po.POCode', $parameter->POCode)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('po.POCode')
    ->orderBy('po.id', 'desc')
    ->first();
    
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
    foreach($periodes as $key => $periode2){
      $start = $periode2->S;
      $end = $periode2->E;

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
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode2->SumQTertanda*($periode2->Amount-($periode2->Amount*$periode2->Discount/100)); 
      $total += $total2[$key];
    }

		if($invoice->PPNT == 1){
			$toss = $invoice->Transport;
			$toss2 = 0;
		}else{
			$toss = 0;
			$toss2 = $invoice->Transport;
		}

    if($invoice->PPNT == 1)
      $totals = number_format((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))-(((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');

    if($invoice->PPNT == 1)
      $Pajak = number_format((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1), 2, ',','.');
    else
      $Pajak = number_format(($total*$invoice->PPN*0.1), 2, ',','.');

		$Transport = number_format(($toss*$invoice->TimesKembali)+($toss*$invoice->Times), 2, ',','.');
		$GrandTotalTransport = number_format(($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times), 2, ',','.');
		
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
    
    if(in_array("showsewapisah", $this->access)){
      return view('pages.invoice.showsewapisah')
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
      ->with('totals', $totals)
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
	
  public function postInvoiceSewa(Request $request, $id)
  {
    $invoice = Invoice::find($id);
    
    $POCode = Transaksi::where('transaksi.Reference', $invoice->Reference)->pluck('POCode');
    
    $invoice->id = $id;
    $invoice->PPN = $request->PPN;
		$invoice->Times = $request->Times;
		$invoice->TimesKembali = $request->TimesKembali;
    $invoice->Discount = $request->Discount;
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
			$invoicepisah->PPN = $invoice->PPN;
			//$invoicepisah->Discount = $invoice->Discount;
			$invoicepisah->TglTerima = $invoice->TglTerima;
			$invoicepisah->Termin = $invoice->Termin;
			$invoicepisah->save();
		}
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update invoice on Invoice '.$request['Invoice'];
    $history->save();

    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('invoice.showsewa', $id);
  }
	
	public function postInvoiceSewaPisah(Request $request, $id)
  {
    $invoicepisah = InvoicePisah::find($id);
    
    $POCode = Transaksi::where('transaksi.Reference', $invoicepisah->Reference)->pluck('POCode');
    
    $invoicepisah->id = $id;
    $invoicepisah->PPN = $request->PPN;
		$invoicepisah->Times = $request->Times;
		$invoicepisah->TimesKembali = $request->TimesKembali;
    $invoicepisah->Discount = $request->Discount;
		$invoicepisah->TglTerima = $request->TglTerima;
		$invoicepisah->Termin = $request->Termin;
    $invoicepisah->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
    $invoicepisah->Catatan = $request->Catatan;
    $invoicepisah->save();
		
		$invoice = Invoice::where('Reference', $invoicepisah->Reference)
		->where('Periode', $invoicepisah->Periode)
		->where('JSC', 'Sewa')
		->first();
    
    $invoice = Invoice::find($invoice->id);
		$invoice->PPN = $invoicepisah->PPN;
		//$invoice->Discount = $invoicepisah->Discount;
		$invoice->TglTerima = $invoicepisah->TglTerima;
		$invoice->Termin = $invoicepisah->Termin;
		$invoice->save();
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update invoice on Invoice Pisah '.$request['Invoice'];
    $history->save();

    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('invoice.showsewapisah', $id);
  }
  
  public function getBA($id)
  {
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
    
    $pocode = PO::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('po.POCode')
    ->orderBy('po.id', 'desc')
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'periode.S',
      'periode.E',
      DB::raw('SUM(isisjkirim.QTertanda) as SumQTertanda'),
      'po.POCode'
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    ->where('periode.Quantity', '!=' , 0)
		->groupBy('transaksi.ICode', 'sjkirim.SJKir', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $firststart = $periodes->pluck('S');
    
    foreach($periodes as $key => $periode2){
      $start = $periode2->S;
      $end = $periode2->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $SE[] = round((($end3[$key] - $start3[$key]) / 86400),1)+1;
      //$pocode[] = $periode2->POCode;
    }
    $Quantity = $periodes->sum('SumQTertanda');
    //$PEO = implode("/", array_unique($pocode));
    $sjkirs = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')->where('transaksi.Reference', $invoice->Reference)->where('JS', 'Sewa')->pluck('SJKir')->toArray();
    $SJKir = join(', ', $sjkirs);
    
    $document = $phpWord->loadTemplate(public_path('/template/BA.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('SJKir', ''.$SJKir.'');
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
      $document->setValue('Quantity'.$key, ''.$periodes->SumQTertanda.'');
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
    $path = sprintf("C:\Users\Public\Documents\BA_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name BA_'.$download);
    return redirect()->route('invoice.showsewa', $id);
  }
  
  function kekata($x) {
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

  function terbilang($x, $style=4) {
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
  
  public function getInvs($id)
  {
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $isisjkirim = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->orderBy('isisjkirim.id', 'desc')
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'transaksi.Amount',
      'periode.S',
      'periode.E',
      DB::raw('SUM(isisjkirim.QTertanda) as SumQTertanda'),
      'po.POCode',
      'po.Discount'
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    ->where('periode.Quantity', '!=' , 0)
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
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode->SumQTertanda*($periode->Amount-($periode->Amount*$periode->Discount/100));
      $total += $total2[$key];
    }

		if($invoice->PPNT == 1){
			$toss = $invoice->Transport;
			$toss2 = 0;
		}else{
			$toss = 0;
			$toss2 = $invoice->Transport;
		}
    
    if($invoice->PPNT == 1)
      $totals = (($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))-(((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan;
    else
      $totals = ($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan;
    
    if($invoice->PPNT == 1)
      $Discount = number_format((((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times)))*$invoice->Discount/100), 0, ',','.');
    else
      $Discount = number_format(((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->Discount/100), 0, ',','.');
    
    if($invoice->PPNT == 1)
      $PPN = number_format((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1), 0, ',','.');
    else
      $PPN = number_format(($total*$invoice->PPN*0.1), 0, ',','.');

		$Transport = number_format(($toss*$invoice->TimesKembali)+($toss*$invoice->Times), 0, ',','.');
    
    $firststart = $periodes->pluck('S');
    
    /*$sjkirs = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')->where('transaksi.Reference', $invoice->Reference)->where('JS', 'Sewa')->pluck('SJKir')->toArray();
    $SJKir = join(', ', $sjkirs);
    $sjkems = Periode::where('Reference', $invoice->Reference)->where('Periode', $invoice->Periode)->where('Deletes', 'Kembali')->pluck('SJKem')->toArray();
    $SJKem = join(', ', $sjkems);*/
    $Quantity = $periodes->sum('SumQTertanda');
    //$PEO = implode("/", array_unique($pocode));
    
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
    $document->setValue('SJKir', ''.$isisjkirim->SJKir.'');
    $document->setValue('S', ''.$firststart[0].'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('DueDate', ''.$duedate.'');
    $document->setValue('POCode', ''.$isisjkirim->POCode.'');
    //$document->setValue('SJKem', ''.$SJKem.'');
    $document->setValue('Quantity', ''.$Quantity.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Discount', ''.$Discount.'');
    $document->setValue('Transport', ''.$Transport.'');
    $document->setValue('PPN', ''.$PPN.'');
    $document->setValue('Totals', ''.number_format($totals, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($totals)).' Rupiah'.'');

    foreach ($periodes as $key => $periode)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$periode->Barang.'');
      $document->setValue('S'.$key, ''.$periode->S.'');
      $document->setValue('E'.$key, ''.$periode->E.'');
      $document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('I'.$key, ''.$I[$key].'');
      $document->setValue('Quantity'.$key, ''.$periode->SumQTertanda.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($periode->Amount-($periode->Amount*$periode->Discount/100), 0, ',', '.').'');
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
    $path = sprintf("C:\Users\Public\Documents\Invs_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name Invs_'.$download);
    return redirect()->route('invoice.showsewa', $id);
  }
  
  public function getInvst($id)
  {
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $isisjkirim = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->orderBy('isisjkirim.id', 'desc')
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'transaksi.Amount',
      'periode.S',
      'periode.E',
      DB::raw('SUM(isisjkirim.QTertanda) as SumQTertanda'),
      'po.POCode',
      'po.Discount'
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    ->where('periode.Quantity', '!=' , 0)
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
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode->SumQTertanda*($periode->Amount-($periode->Amount*$periode->Discount/100));
      //$total += $total2[$key];
    }
    
    if($invoice->PPNT == 1){
			$toss = $invoice->Transport;
			$toss2 = 0;
		}else{
			$toss = 0;
			$toss2 = $invoice->Transport;
		}
    
    if($invoice->PPNT == 1)
      $totals = (($total+($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times))-(((($total+($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan;
    else
      $totals = ($total*$invoice->PPN*0.1)+$total+($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan;
    
    if($invoice->PPNT == 1)
      $Discount = number_format((((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)+($toss*$invoice->TimesKembali)))*$invoice->Discount/100), 0, ',','.');
    else
      $Discount = number_format(((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)+($toss*$invoice->TimesKembali))*$invoice->Discount/100), 0, ',','.');
    
    if($invoice->PPNT == 1)
      $PPN = number_format((($total+($toss*$invoice->TimesKembali)+($toss*$invoice->Times))*$invoice->PPN*0.1), 0, ',','.');
    else
      $PPN = number_format(($total*$invoice->PPN*0.1), 0, ',','.');

		$GrandTotalTransport = number_format(($toss2*$invoice->TimesKembali)+($toss2*$invoice->Times), 0, ',','.');
    
    $firststart = $periodes->pluck('S');
    $Quantity = $periodes->sum('SumQTertanda');
    
    $document = $phpWord->loadTemplate(public_path('/template/Invst.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('SJKir', ''.$isisjkirim->SJKir.'');
    $document->setValue('S', ''.$firststart[0].'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('DueDate', ''.$duedate.'');
    $document->setValue('POCode', ''.$isisjkirim->POCode.'');
    $document->setValue('Quantity', ''.$Quantity.'');
    $document->setValue('Total', ''.number_format(0, 0, ',','.').'');
    $document->setValue('Discount', ''.$Discount.'');
    $document->setValue('Transport', ''.$GrandTotalTransport.'');
    $document->setValue('PPN', ''.$PPN.'');
    $document->setValue('Totals', ''.number_format($totals, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($totals)).' Rupiah'.'');

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
      $document->setValue('Price'.$key, ''.number_format($periode->Amount-($periode->Amount*$periode->Discount/100), 0, ',', '.').'');
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
    $path = sprintf("C:\Users\Public\Documents\Invst_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name Invst_'.$download);
    return redirect()->route('invoice.showsewa', $id);
  }

  public function getInvoiceJual($id)
  {
    $parameter = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.Transport',
      'pocustomer.PPNT',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Reference', $parameter->Reference)
    ->where('invoice.Invoice', $parameter->Invoice)
    ->first();
    
    $transaksis = Transaksi::select([
      'isisjkirim.QKirim',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'po.Discount',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
    ])
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->rightJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Jual')
    ->get();
    
    $pocode = PO::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Jual')
    ->groupBy('po.POCode')
    ->orderBy('po.id', 'desc')
    ->first();
    
    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi2){
      $total2[] = $transaksi2->QKirim * ($transaksi2->Amount-$transaksi2->Amount*$transaksi2->Discount/100); 
      $total += $total2[$x];
      $x++;
    }
		
		if($invoice->PPNT == 1){
			$toss = $invoice->Transport;
			$toss2 = 0;
		}else{
			$toss = 0;
			$toss2 = $invoice->Transport;
		}
    
    if($invoice->PPNT == 1)
      $totals = "Rp. ".number_format((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times))-(((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else
      $totals = "Rp. ".number_format(($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');

    if($invoice->PPNT == 1)
      $Pajak = number_format((($total+($toss*$invoice->Times))*$invoice->PPN*0.1), 2, ',','.');
    else
      $Pajak = number_format(($total*$invoice->PPN*0.1), 2, ',','.');
    
    $Transport = number_format($toss*$invoice->Times, 2, ',','.');
		$GrandTotalTransport = number_format($toss2*$invoice->Times, 2, ',','.');
		
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
		
    if(in_array("showjual", $this->access)){
      return view('pages.invoice.showjual')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('transaksis', $transaksis)
      ->with('pocode', $pocode)
      ->with('total', $total)
      ->with('totals', $totals)
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
	
  public function postInvoiceJual(Request $request, $id)
  {
    $invoice = Invoice::find($id);
    
    $POCode = Transaksi::where('transaksi.Reference', $invoice->Reference)->pluck('POCode');
    
    $invoice->id = $id;
    $invoice->PPN = $request->PPN;
		$invoice->Times = $request->Times;
    $invoice->Discount = $request->Discount;
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
    
    return redirect()->route('invoice.showjual', $id);
  }
	
	public function getInvoiceJualPisah($id)
  {
    $parameter = InvoicePisah::find($id);
    
    $invoice = InvoicePisah::select([
      'invoicepisah.*',
      'pocustomer.Transport',
      'pocustomer.PPNT',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoicepisah.Invoice', $parameter->Invoice)
    ->first();
    
    $transaksis = Transaksi::select([
      'isisjkirim.QKirim',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'po.Discount',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
    ])
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->rightJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Jual')
    ->get();
    
    $pocode = PO::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Jual')
    ->groupBy('po.POCode')
    ->orderBy('po.id', 'desc')
    ->first();
    
    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi2){
      $total2[] = $transaksi2->QKirim * ($transaksi2->Amount-$transaksi2->Amount*$transaksi2->Discount/100); 
      $total += $total2[$x];
      $x++;
    }
		
		if($invoice->PPNT == 1){
			$toss = $invoice->Transport;
			$toss2 = 0;
		}else{
			$toss = 0;
			$toss2 = $invoice->Transport;
		}
    
    if($invoice->PPNT == 1)
      $totals = "Rp. ".number_format((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times))-(((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else
      $totals = "Rp. ".number_format(($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');

    if($invoice->PPNT == 1)
      $Pajak = number_format((($total+($toss*$invoice->Times))*$invoice->PPN*0.1), 2, ',','.');
    else
      $Pajak = number_format(($total*$invoice->PPN*0.1), 2, ',','.');
    
    $Transport = number_format($toss*$invoice->Times, 2, ',','.');
		$GrandTotalTransport = number_format($toss2*$invoice->Times, 2, ',','.');
		
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
		
    if(in_array("showjualpisah", $this->access)){
      return view('pages.invoice.showjualpisah')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('transaksis', $transaksis)
      ->with('pocode', $pocode)
      ->with('total', $total)
      ->with('totals', $totals)
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
	
	public function postInvoiceJualPisah(Request $request, $id)
  {
    $invoice = InvoicePisah::find($id);
    
    $POCode = Transaksi::where('transaksi.Reference', $invoice->Reference)->pluck('POCode');
    
    $invoice->id = $id;
    $invoice->PPN = $request->PPN;
		$invoice->Times = $request->Times;
    $invoice->Discount = $request->Discount;
		$invoice->TglTerima = $request->TglTerima;
		$invoice->Termin = $request->Termin;
    $invoice->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
    $invoice->Catatan = $request->Catatan;
    $invoice->save();

    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update invoice on Invoice Pisah '.$request['Invoice'];
    $history->save();
    
    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('invoice.showjualpisah', $id);
  }
  
  public function getInvj($id)
  {
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $transaksis = Transaksi::select([
      'inventory.Type',
      'isisjkirim.QKirim',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'po.Discount',
      'transaksi.*',
    ])
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->rightJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
    ->get();
    
    $isisjkirim = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
    ->orderBy('isisjkirim.id', 'desc')
    ->first();

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QKirim * ($transaksi->Amount-$transaksi->Amount*$transaksi->Discount/100); 
      $total += $total2[$x];
      $x++;
    }
    
		if($invoice->PPNT == 1){
			$toss = $invoice->Transport;
			$toss2 = 0;
		}else{
			$toss = 0;
			$toss2 = $invoice->Transport;
		}
    
    if($invoice->PPNT == 1)
      $totals = (($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times))-(((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan;
    else
      $totals = ($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan;
    
    if($invoice->PPNT == 1)
      $Discount = number_format((((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times)))*$invoice->Discount/100), 0, ',','.');
    else
      $Discount = number_format(((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times))*$invoice->Discount/100), 0, ',','.');
    
    if($invoice->PPNT == 1)
      $PPN = number_format((($total+($toss*$invoice->Times))*$invoice->PPN*0.1), 0, ',','.');
    else
      $PPN = number_format(($total*$invoice->PPN*0.1), 0, ',','.');
		
    $Transport = number_format($toss*$invoice->Times, 0, ',','.');
    
    if($invoice->PPN==1)
      $document = $phpWord->loadTemplate(public_path('/template/Invjp.docx'));
    else
      $document = $phpWord->loadTemplate(public_path('/template/Invjnp.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('SJKir', ''.$isisjkirim->SJKir.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$isisjkirim->Tgl.'');
    $document->setValue('POCode', ''.$isisjkirim->POCode.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Discount', ''.$Discount.'');
    $document->setValue('Transport', ''.$Transport.'');
    $document->setValue('PPN', ''.$PPN.'');
    $document->setValue('Totals', ''.number_format($totals, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($totals)).' Rupiah'.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Type'.$key, ''.$transaksi->Type.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      $document->setValue('Quantity'.$key, ''.$transaksi->QKirim.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($transaksi->Amount-$transaksi->Amount*$transaksi->Discount/100, 0, ',', '.').'');
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
    $path = sprintf("C:\Users\Public\Documents\Invj_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name Invj_'.$download);
    return redirect()->route('invoice.showjual', $id);
  }
  
  public function getInvjt($id)
  {
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $invoice = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'pocustomer.*',
      'project.*',
      'customer.*',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Invoice', $invoice->Invoice)
    ->first();
    
    $transaksis = Transaksi::select([
      'inventory.Type',
      'isisjkirim.QKirim',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'po.Discount',
      'transaksi.*',
    ])
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->rightJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
    ->get();
    
    $isisjkirim = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Jual')
    ->orderBy('isisjkirim.id', 'desc')
    ->first();

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi){
      $total2[] = $transaksi->QKirim * ($transaksi->Amount-$transaksi->Amount*$transaksi->Discount/100); 
      //$total += $total2[$x];
      $x++;
    }
    
    if($invoice->PPNT == 1){
			$toss = $invoice->Transport;
			$toss2 = 0;
		}else{
			$toss = 0;
			$toss2 = $invoice->Transport;
		}
    
    if($invoice->PPNT == 1)
      $totals = (($total+($toss2*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss2*$invoice->Times))-(((($total+($toss2*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss2*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan;
    else
      $totals = ($total*$invoice->PPN*0.1)+$total+($toss2*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss2*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan;
    
    if($invoice->PPNT == 1)
      $Discount = number_format((((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times)))*$invoice->Discount/100), 0, ',','.');
    else
      $Discount = number_format(((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times))*$invoice->Discount/100), 0, ',','.');
    
    if($invoice->PPNT == 1)
      $PPN = number_format((($total+($toss*$invoice->Times))*$invoice->PPN*0.1), 0, ',','.');
    else
      $PPN = number_format(($total*$invoice->PPN*0.1), 0, ',','.');
    
    $GrandTotalTransport = number_format($toss2*$invoice->Times, 0, ',','.');
		
    $document = $phpWord->loadTemplate(public_path('/template/Invjt.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompAlamat', ''.$invoice->CompAlamat.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('SJKir', ''.$isisjkirim->SJKir.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$isisjkirim->Tgl.'');
    $document->setValue('POCode', ''.$isisjkirim->POCode.'');
    $document->setValue('Total', ''.number_format(0, 0, ',','.').'');
    $document->setValue('Discount', ''.$Discount.'');
    $document->setValue('Transport', ''.$GrandTotalTransport.'');
    $document->setValue('PPN', ''.$PPN.'');
    $document->setValue('Totals', ''.number_format($totals, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($totals)).' Rupiah'.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Type'.$key, ''.$transaksi->Type.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      $document->setValue('Quantity'.$key, ''.'0'.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($transaksi->Amount-$transaksi->Amount*$transaksi->Discount/100, 0, ',', '.').'');
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
    $path = sprintf("C:\Users\Public\Documents\Invjt_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name Invjt_'.$download);
    return redirect()->route('invoice.showjual', $id);
  }
  
  public function getInvoiceClaim($id){
    $parameter = Invoice::find($id);
    
    $invoice = Invoice::select([
      'invoice.*',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Reference', $parameter->Reference)
    ->where('invoice.Invoice', $parameter->Invoice)
    ->first();
    
    $pocode = PO::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('po.POCode')
    ->orderBy('po.id', 'desc')
    ->first();
    
    $transaksis = TransaksiClaim::select([
      'isisjkirim.SJKir',
      'transaksiclaim.*',
      'transaksi.Reference',
      'transaksi.Barang',
      'transaksi.QSisaKem',
      'project.Project',
      'customer.*',
    ])
    ->leftJoin('isisjkirim', 'transaksiclaim.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'transaksiclaim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksiclaim.Periode', $parameter->Periode)
    ->groupBy('transaksiclaim.Claim', 'transaksiclaim.Tgl', 'transaksiclaim.Claim')
    ->orderBy('transaksiclaim.id', 'asc')
    ->get();

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi2){
      $total2[] = $transaksi2->QClaim * $transaksi2->Amount; 
      $total += $total2[$x];
      $x++;
    }
    
    if($invoice->PPNT == 1)
      $totals = number_format((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1)+($total+($invoice->Transport*$invoice->Times))-(((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1)+($total+($invoice->Transport*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+($invoice->Transport*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($invoice->Transport*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    
    if($invoice->PPNT == 1)
      $Pajak = number_format((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1), 0, ',','.');
    else
      $Pajak = number_format(($total*$invoice->PPN*0.1), 2, ',','.');
    
		$tglterima = str_replace('/', '-', $invoice->TglTerima);
		$duedate = date('d/m/Y', strtotime($tglterima."+".$invoice->Termin." days"));
		
    if(in_array("showclaim", $this->access)){
      return view('pages.invoice.showclaim')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('pocode', $pocode)
      ->with('transaksis', $transaksis)
      ->with('total', $total)
      ->with('total2', $total2)
      ->with('totals', $totals)
			->with('duedate', $duedate)
      ->with('Pajak', $Pajak)
      ->with('top_menu_sel', 'menu_invoice')
      ->with('page_title', 'Invoice Claim')
      ->with('page_description', 'View');
    }else
      return redirect()->back();
	}
  
  public function postInvoiceClaim(Request $request, $id)
    {
    	$invoice = Invoice::find($id);
      
      $input = Input::all();
      
      $invoice->id = $id;
      $invoice->PPN = $request->PPN;
    	$invoice->Discount = $request->Discount;
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

    public function index()
    {
			
		if(Auth::user()->access == 'Admin'){
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
		}else{
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
			->where('pocustomer.PPN', $this->PPNNONPPN)
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}
		
		if(Auth::user()->access == 'Admin'){
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
		}else{
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
			->where('pocustomer.PPN', $this->PPNNONPPN)
			->groupBy('invoicepisah.Reference', 'invoicepisah.POCode', 'invoicepisah.Periode')
			->get();
		}
    
		if(Auth::user()->access == 'Admin'){
			$invoicej = Invoice::select([
					'invoice.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->whereRaw('invoice.Reference = periode.Reference')
					->where('periode.Deletes', 'Jual');
				})
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}else{
			$invoicej = Invoice::select([
					'invoice.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			->where('pocustomer.INVP', 0)
			->whereExists(function($query)
				{
					$query->select('periode.Reference')
					->from('periode')
					->whereRaw('invoice.Reference = periode.Reference')
					->where('periode.Deletes', 'Jual');
				})
			->where('pocustomer.PPN', $this->PPNNONPPN)
			->groupBy('invoice.Reference', 'invoice.Periode')
			->get();
		}
		
		if(Auth::user()->access == 'Admin'){
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
			->groupBy('invoicepisah.Reference', 'invoicepisah.Periode')
			->get();
		}else{
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
			->where('pocustomer.PPN', $this->PPNNONPPN)
			->groupBy('invoicepisah.Reference', 'invoicepisah.Periode')
			->get();
		}
    
		if(Auth::user()->access == 'Admin'){
			$invoicec = Invoice::select([
				'invoice.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Claim')
			->groupBy('invoice.Periode')
			->get();
		}else{
			$invoicec = Invoice::select([
				'invoice.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Claim')
			->where('pocustomer.PPN', $this->PPNNONPPN)
			->groupBy('invoice.Periode')
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
    
    public function getInvc($id)
  {
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
    
    $sjkirim = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('transaksiclaim', 'transaksi.Purchase', '=', 'transaksiclaim.Purchase')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksiclaim.Periode', $invoice->Periode)
    ->where('JS', 'Sewa')
    ->orderBy('isisjkirim.id', 'asc')
    ->first();
    
    $pocode = PO::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('po.POCode')
    ->orderBy('po.id', 'desc')
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
      'transaksi.Reference',
      'transaksi.Barang',
      'transaksi.QSisaKem',
      'project.Project',
      'customer.*',
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
    
    if($invoice->PPNT == 1)
      $totals = (($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1)+($total+($invoice->Transport*$invoice->Times))-(((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1)+($total+($invoice->Transport*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan;
    else
      $totals = ($total*$invoice->PPN*0.1)+$total+($invoice->Transport*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($invoice->Transport*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan;
    
    if($invoice->PPNT == 1)
      $Discount = number_format((((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1)+($total+($invoice->Transport*$invoice->Times)))*$invoice->Discount/100), 0, ',','.');
    else
      $Discount = number_format(((($total*$invoice->PPN*0.1)+$total+($invoice->Transport*$invoice->Times))*$invoice->Discount/100), 0, ',','.');
    
    if($invoice->PPNT == 1)
      $PPN = number_format((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1), 0, ',','.');
    else
      $PPN = number_format(($total*$invoice->PPN*0.1), 0, ',','.');
    
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
    $document->setValue('SJKir', ''.$sjkirim->SJKir.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('Tgl', ''.$invoice->Tgl.'');
    $document->setValue('DueDate', ''.$duedate.'');
    $document->setValue('POCode', ''.$pocode->POCode.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Discount', ''.$Discount.'');
    $document->setValue('PPN', ''.$PPN.'');
    $document->setValue('Totals', ''.number_format($totals, 0, ',','.').'');
    $document->setValue('Terbilang', ''.$this->terbilang(round($totals)).' Rupiah'.'');

    foreach ($transaksis as $key => $transaksi)
    {
      $key2 = $key+1;
      $document->setValue('Key'.$key, ''.$key2.'');
      $document->setValue('Barang'.$key, ''.$transaksi->Barang.'');
      $document->setValue('S'.$key, ''.$transaksi->S.'');
      $document->setValue('E'.$key, ''.$transaksi->E.'');
      $document->setValue('SE'.$key, ''.$SE[$key].'');
      $document->setValue('I'.$key, ''.$I[$key].'');
      $document->setValue('Quantity'.$key, ''.$transaksi->QClaim.'');
      $document->setValue('Sat'.$key, 'PCS');
      $document->setValue('Price'.$key, ''.number_format($transaksi->Amount-($transaksi->Amount*$transaksi->Discount/100), 0, ',', '.').'');
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
    $path = sprintf("C:\Users\Public\Documents\Invc_", $user);
    $clear = str_replace("/","_",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Server Public Documents file name Invc_'.$download);
    return redirect()->route('invoice.showclaim', $id);
  }
    
    public function getLunas($id)
    {
    	return view('pages.invoice.lunas')
      ->with('id', $id);
    }
    
    public function postLunas(Request $request, $id)
    {
      $invoice = Invoice::find($id);
      
      if($invoice->Lunas == 0){
        $lunas = 1;
      }else{
        $lunas = 0;
      }
      
      Invoice::where('invoice.id', $id)
      ->update(['Invoice.Lunas' => $lunas]);
      
      return redirect()->route('invoice.index');
    }
}
