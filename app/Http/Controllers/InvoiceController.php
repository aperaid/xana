<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Periode;
use App\Transaksi;
use App\TransaksiClaim;
use App\PO;
use App\History;
use Session;
use DB;
use Auth;

class InvoiceController extends Controller
{
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
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) AS SumQuantity'),
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
    ->groupBy('sjkirim.SJKir', 'periode.S', 'periode.Deletes')
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
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode2->SumQuantity*($periode2->Amount-($periode2->Amount*$periode2->Discount/100)); 
      $total += $total2[$key];
    }
    
    if($invoice->Times > 0 || $invoice->TimesKembali > 0)
      $toss = $invoice->Transport;
    else
      $toss = 0;
    
    if($invoice->TimesKembali > 0 && $invoice->PPNT == 1)
      $totals = number_format((($total+($toss*$invoice->TimesKembali))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali))-(((($total+($toss*$invoice->TimesKembali))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else if($invoice->TimesKembali > 0 && $invoice->PPNT == 0)
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else if($invoice->Times > 0 && $invoice->PPNT == 1)
      $totals = number_format((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times))-(((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else if($invoice->Times > 0 && $invoice->PPNT == 0)
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+$toss-((($total*$invoice->PPN*0.1)+$total+$toss)*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    
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
    
    if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='INVPPN'||Auth::user()->access()=='INVNONPPN'){
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
      ->with('total2', $total2)
      ->with('top_menu_sel', 'menu_invoice')
      ->with('page_title', 'Invoice Sewa')
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
    $invoice->Discount = $request->Discount;
    $invoice->Pembulatan = str_replace(".","",substr($request->Pembulatan, 3));
    $invoice->Catatan = $request->Catatan;
    $invoice->save();
    
    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Update invoice on Invoice '.$request['Invoice'];
    $history->save();

    Session::flash('message', 'Update is successful!');
    
    return redirect()->route('invoice.showsewa', $id);
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
      DB::raw('SUM(periode.Quantity) AS SumQuantity'),
      'po.POCode'
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    ->where('periode.Quantity', '!=' , 0)
    ->groupBy('periode.Purchase', 'periode.S', 'periode.Deletes')
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
    $Quantity = $periodes->sum('SumQuantity');
    //$PEO = implode("/", array_unique($pocode));
    
    $document = $phpWord->loadTemplate(public_path('/template/BA.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
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
    $path = sprintf("C:\Users\%s\Desktop\BA_", $user);
    $clear = str_replace("/","",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Desktop file name BA_'.$download);
    return redirect()->route('invoice.showsewa', $id);
  }
  
  public function getInv($id)
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
    
    $pocode = PO::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->groupBy('po.POCode')
    ->orderBy('po.id', 'desc')
    ->first();
    
    $periodes = Periode::select([
      'transaksi.Barang',
      'transaksi.Amount',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) AS SumQuantity'),
      'po.POCode',
      'po.Discount'
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->where('transaksi.Reference', $invoice->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $invoice->Periode)
    ->where('periode.Quantity', '!=' , 0)
    ->groupBy('periode.Purchase', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
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
      
      $total2[] = ((($end3[$key] - $start3[$key]) / 86400)+1)/$Days2[$key]*$periode2->SumQuantity*($periode2->Amount-($periode2->Amount*$periode2->Discount/100));
      $total += $total2[$key];
    }
    
    if($invoice->Times > 0 || $invoice->TimesKembali > 0)
      $toss = $invoice->Transport;
    else
      $toss = 0;
    
    if($invoice->TimesKembali > 0 && $invoice->PPNT == 1)
      $totals = number_format((($total+($toss*$invoice->TimesKembali))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali))-(((($total+($toss*$invoice->TimesKembali))*$invoice->PPN*0.1)+($total+($toss*$invoice->TimesKembali)))*$invoice->Discount/100)-$invoice->Pembulatan, 0, ',','.');
    else if($invoice->TimesKembali > 0 && $invoice->PPNT == 0)
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->TimesKembali))*$invoice->Discount/100)-$invoice->Pembulatan, 0, ',','.');
    else if($invoice->Times > 0 && $invoice->PPNT == 1)
      $totals = number_format((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times))-(((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)+($total+($toss*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan, 0, ',','.');
    else if($invoice->Times > 0 && $invoice->PPNT == 0)
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($toss*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan, 0, ',','.');
    else
      $totals = number_format(($total*$invoice->PPN*0.1)+$total+$toss-((($total*$invoice->PPN*0.1)+$total+$toss)*$invoice->Discount/100)-$invoice->Pembulatan, 0, ',','.');
    
    if($invoice->TimesKembali > 0 && $invoice->PPNT == 1)
      $PPN = number_format(((($total+($toss*$invoice->TimesKembali))*$invoice->PPN*0.1)), 0, ',','.');
    else if($invoice->TimesKembali > 0 && $invoice->PPNT == 0)
      $PPN = number_format((($total*$invoice->PPN*0.1)), 0, ',','.');
    else if($invoice->Times > 0 && $invoice->PPNT == 1)
      $PPN = number_format(((($total+($toss*$invoice->Times))*$invoice->PPN*0.1)), 0, ',','.');
    else if($invoice->Times > 0 && $invoice->PPNT == 0)
      $PPN = number_format((($total*$invoice->PPN*0.1)), 0, ',','.');
    else
      $PPN = number_format((($total*$invoice->PPN*0.1)+$toss), 0, ',','.');
    
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
    
    $sjkems = Periode::where('Reference', $invoice->Reference)->where('Deletes', 'Kembali')->pluck('SJKem')->toArray();
    $SJKem = join(', ', $sjkems);
    $Quantity = $periodes->sum('SumQuantity');
    //$PEO = implode("/", array_unique($pocode));
    
    $document = $phpWord->loadTemplate(public_path('/template/Inv.docx'));
    
    $document->setValue('Company', ''.$invoice->Company.'');
    $document->setValue('CompPhone', ''.$invoice->CompPhone.'');
    $document->setValue('PCode', ''.$invoice->PCode.'');
    $document->setValue('Project', ''.$invoice->Project.'');
    $document->setValue('Invoice', ''.$invoice->Invoice.'');
    $document->setValue('S', ''.$firststart[0].'');
    $document->setValue('E', ''.$end.'');
    $document->setValue('SJKem', ''.$SJKem.'');
    $document->setValue('Quantity', ''.$Quantity.'');
    $document->setValue('Total', ''.number_format($total, 0, ',','.').'');
    $document->setValue('Transport', ''.number_format($toss, 0, ',','.').'');
    $document->setValue('PPN', ''.$PPN.'');
    $document->setValue('Totals', ''.$totals.'');

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
    $path = sprintf("C:\Users\%s\Desktop\Inv_", $user);
    $clear = str_replace("/","",$invoice->Invoice);
    $download = sprintf('%s.docx', $clear);
    
    $document->saveAs($path.$download);
    
    Session::flash('message', 'Downloaded to Desktop file name Inv_'.$download);
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
    
    if($invoice->PPNT == 1)
      $totals = "Rp. ".number_format((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1)+($total+($invoice->Transport*$invoice->Times))-(((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1)+($total+($invoice->Transport*$invoice->Times)))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    else
      $totals = "Rp. ".number_format(($total*$invoice->PPN*0.1)+$total+($invoice->Transport*$invoice->Times)-((($total*$invoice->PPN*0.1)+$total+($invoice->Transport*$invoice->Times))*$invoice->Discount/100)-$invoice->Pembulatan, 2, ',','.');
    
    if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='INVPPN'||Auth::user()->access()=='INVNONPPN'){
      return view('pages.invoice.showjual')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('transaksis', $transaksis)
      ->with('pocode', $pocode)
      ->with('total', $total)
      ->with('totals', $totals)
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
    	$invoice->Discount = $request->Discount;
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
    
    $transaksis = TransaksiClaim::select([
      'isisjkirim.SJKir',
      'transaksiclaim.*',
      DB::raw('SUM(transaksiclaim.QClaim) AS SumQClaim'),
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
    
    $PPN = $transaksis->first()->PPN;

    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi2){
      $total2[] = $transaksi2->QClaim * $transaksi2->Amount; 
      $total += $total2[$x];
      $x++;
    }
    
    if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='INVPPN'||Auth::user()->access()=='INVNONPPN'){
      return view('pages.invoice.showclaim')
      ->with('url', 'invoice')
      ->with('invoice', $invoice)
      ->with('transaksis', $transaksis)
      ->with('total', $total)
      ->with('total2', $total2)
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
    	$invoice->Discount = str_replace(".","",substr($request->Discount, 3));
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
    $invoices = Invoice::select([
        'invoice.*',
        'project.Project',
        'customer.Company',
      ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('JSC', 'Sewa')
    ->whereExists(function($query)
      {
        $query->select('periode.Reference')
        ->from('periode')
        ->whereRaw('invoice.Reference = periode.Reference')
        ->where('periode.Deletes', 'Sewa');
      })
    ->groupBy('invoice.Reference', 'invoice.Periode')
    ->get();
    
    $invoicej = Invoice::select([
        'invoice.*',
        'project.Project',
        'customer.Company',
      ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('JSC', 'Jual')
    ->whereExists(function($query)
      {
        $query->select('periode.Reference')
        ->from('periode')
        ->whereRaw('invoice.Reference = periode.Reference')
        ->where('periode.Deletes', 'Jual');
      })
    ->groupBy('invoice.Reference', 'invoice.Periode')
    ->get();
    
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

    if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='INVPPN'||Auth::user()->access()=='INVNONPPN'){
      return view('pages.invoice.indexs')
      ->with('url', 'invoice')
      ->with('invoicess', $invoices)
      ->with('invoicejs', $invoicej)
      ->with('invoicecs', $invoicec)
      ->with('top_menu_sel', 'menu_invoice')
      ->with('page_title', 'Invoice')
      ->with('page_description', 'Index');
    }else
      return redirect()->back();
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
