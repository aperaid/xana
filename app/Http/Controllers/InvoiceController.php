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
use Session;
use DB;

class InvoiceController extends Controller
{
  public function getInvoiceSewa($id){
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
    
    $periodes = Periode::select([
      'sjkirim.SJKir',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) AS SumQuantity'),
      'periode.SJKem',
      'periode.Deletes',
      'periode.Periode',
      'po.Transport',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $parameter->Periode)
    ->where('periode.Quantity', '!=' , 0)
    ->groupBy('periode.Purchase', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $transport = $periodes->first()->Transport;
    
    $total = 0;
    $x=0;
    foreach($periodes as $periode2){
      $start = $periode2->S;
      $end = $periode2->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3[] = strtotime($start2);
      $end3[] = strtotime($end2);

      $SE[] = round((($end3[$x] - $start3[$x]) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2[] = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $I[] = round(((($end3[$x] - $start3[$x]) / 86400)+1)/$Days2[$x], 4);
      
      $total2[] = ((($end3[$x] - $start3[$x]) / 86400)+1)/$Days2[$x]*$periode2->SumQuantity*$periode2->Amount; 
      $total += $total2[$x];
      $x++;
    }
    if ($invoice->Periode == 1){
      $toss = $transport; 
    }else $toss = 0;
    
    $pocodes = Periode::distinct()
    ->select('transaksi.POCode')
    ->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Sewa')
    ->where('periode.Periode', $parameter->Periode)
    ->where('periode.Quantity', '!=', 0)
    ->groupBy('periode.Purchase', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    return view('pages.invoice.showsewa')
    ->with('url', 'invoice')
    ->with('invoice', $invoice)
    ->with('periodes', $periodes)
    ->with('transport', $transport)
    ->with('pocodes', $pocodes)
    ->with('SE', $SE)
    ->with('Days2', $Days2)
    ->with('I', $I)
    ->with('toss', $toss)
    ->with('total', $total)
    ->with('total2', $total2)
    ->with('top_menu_sel', 'menu_invoice')
    ->with('page_title', 'Invoice Sewa')
    ->with('page_description', 'View');
	}
  
  public function postInvoiceSewa(Request $request, $id)
    {
    	$invoice = Invoice::find($id);
      
      $po = PO::where('po.POCode', $invoice -> POCode);
      $poid = $po->pluck('id');
      
      $input = Input::all();
      $pos = $poid;
      foreach ($pos as $key => $po)
      {
        $po = PO::find($pos[$key]);
        $po->Transport = str_replace(".","",substr($request->Transport, 3));
        $po->save();
      }
      
      $invoice->id = $id;
      $invoice->PPN = $request->PPN;
    	$invoice->Discount = str_replace(".","",substr($request->Discount, 3));
      $invoice->Catatan = $request->Catatan;
    	$invoice->save();

      Session::flash('message', 'Update is successful!');
      
    	return redirect()->route('invoice.showsewa', $id);
    }
  
  public function getInvoiceJual($id){
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
    
    $transaksis = Transaksi::select([
      'isisjkirim.QKirim',
      'sjkirim.SJKir',
      'sjkirim.Tgl',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
      'po.Transport',
    ])
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->rightJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Jual')
    ->get();

    $transport = $transaksis->first()->Transport;
    
    $pocodes = transaksi::distinct()
    ->select('transaksi.POCode')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->rightJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $parameter->Reference)
    ->where('transaksi.JS', 'Jual')
    ->get();
    
    $total = 0;
    $x=0;
    foreach($transaksis as $transaksi2){
      $total2[] = $transaksi2->QKirim * $transaksi2->Amount; 
      $total += $total2[$x];
      $x++;
    }
    if ($invoice->Periode == 1){
      $toss = $transport; 
    }else $toss = 0;
    
    return view('pages.invoice.showjual')
    ->with('url', 'invoice')
    ->with('invoice', $invoice)
    ->with('transaksis', $transaksis)
    ->with('transport', $transport)
    ->with('pocodes', $pocodes)
    ->with('total', $total)
    ->with('total2', $total2)
    ->with('top_menu_sel', 'menu_invoice')
    ->with('page_title', 'Invoice Jual')
    ->with('page_description', 'View');
	}
  
  public function postInvoiceJual(Request $request, $id)
    {
    	$invoice = Invoice::find($id);
      
      $po = PO::where('po.POCode', $invoice -> POCode);
      $poid = $po->pluck('id');
      
      $input = Input::all();
      $pos = $poid;
      foreach ($pos as $key => $po)
      {
        $po = PO::find($pos[$key]);
        $po->Transport = str_replace(".","",substr($request->Transport, 3));
        $po->save();
      }
      
      $invoice->id = $id;
      $invoice->PPN = $request->PPN;
    	$invoice->Discount = str_replace(".","",substr($request->Discount, 3));
      $invoice->Catatan = $request->Catatan;
    	$invoice->save();

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
    
    return view('pages.invoice.showclaim')
    ->with('url', 'invoice')
    ->with('invoice', $invoice)
    ->with('transaksis', $transaksis)
    ->with('total', $total)
    ->with('total2', $total2)
    ->with('top_menu_sel', 'menu_invoice')
    ->with('page_title', 'Invoice Claim')
    ->with('page_description', 'View');
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

    return view('pages.invoice.indexs')
    ->with('url', 'invoice')
    ->with('invoicess', $invoices)
    ->with('invoicejs', $invoicej)
    ->with('invoicecs', $invoicec)
    ->with('top_menu_sel', 'menu_invoice')
    ->with('page_title', 'Invoice')
    ->with('page_description', 'Index');
    }
}
