<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Periode;
use Session;
use DB;

class InvoiceController extends Controller
{
  public function getInvoiceSewa(){
    $ReferenceParam = Input::get('Reference');
    $InvoiceParam = Input::get('Invoice');
    $JSParam = Input::get('JS');
    $PeriodeParam = Input::get('Periode');
    
    $invoice = Invoice::select([
      'invoice.*',
      'project.Project',
      'customer.Company',
    ])
    ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
    ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
    ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
    ->where('invoice.Reference', $ReferenceParam)
    ->where('invoice.Invoice', $InvoiceParam)
    ->first();
    
    $periode = Periode::select([
      'sjkirim.SJKir',
      'transaksi.Purchase',
      'transaksi.Barang',
      'transaksi.Amount',
      'transaksi.POCode',
      'periode.S',
      'periode.E',
      DB::raw('SUM(periode.Quantity) AS Quantity'),
      'periode.SJKem',
      'periode.Deletes',
      'periode.Periode',
      'po.Transport',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $ReferenceParam)
    ->where('transaksi.JS', $JSParam)
    ->where('periode.Periode', $PeriodeParam)
    ->where('periode.Quantity', '!=' , 0)
    ->groupBy('periode.Purchase', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    $transport = Periode::select([
      'po.Transport',
    ])
    ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
    ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
    ->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
    ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
    ->where('transaksi.Reference', $ReferenceParam)
    ->where('transaksi.JS', $JSParam)
    ->where('periode.Periode', $PeriodeParam)
    ->where('periode.Quantity', '!=' , 0)
    ->groupBy('periode.Purchase', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->first();
    
    $total = 0;
    foreach($periode as $periodes){
      $start = $periodes->S;
      $end = $periodes->E;

      $start2 = str_replace('/', '-', $start);
      $end2 = str_replace('/', '-', $end);
      $start3 = strtotime($start2);
      $end3 = strtotime($end2);

      $SE = round((($end3 - $start3) / 86400),1)+1;

      $Days = str_replace('/', ',', $start);
      $M = substr($Days, 3, -5);
      $Y = substr($Days, 6);
      $Days2 = cal_days_in_month(CAL_GREGORIAN, $M, $Y);
      
      $total2 = ((($end3 - $start3) / 86400)+1)/$Days2*$periodes->Quantity*$periodes->Amount; 
      $total += $total2;
    }
    if ($invoice->Periode == 1){
      $toss = $transport->Transport; 
    }else $toss = 0;
    
    $pocode = Periode::distinct()
    ->select('transaksi.POCode')
    ->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
    ->where('transaksi.Reference', $ReferenceParam)
    ->where('transaksi.JS', $JSParam)
    ->where('periode.Periode', $PeriodeParam)
    ->where('periode.Quantity', '!=', 0)
    ->groupBy('periode.Purchase', 'periode.S', 'periode.Deletes')
    ->orderBy('periode.id', 'asc')
    ->get();
    
    return view('pages/invoice/showsewa')
    ->with('invoice', $invoice)
    ->with('periodes', $periode)
    ->with('transport', $transport)
    ->with('pocodes', $pocode)
    ->with('SE', $SE)
    ->with('Days2', $Days2)
    ->with('end3', $end3)
    ->with('start3', $start3)
    ->with('toss', $toss)
    ->with('total', $total)
    ->with('total2', $total2)
    ->with('top_menu_sel', 'menu_invoice')
    ->with('page_title', 'Invoice Sewa')
    ->with('page_description', 'View');
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
        ->whereRaw('invoice.Reference = periode.Reference AND periode.Deletes = "Sewa"');
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
        ->whereRaw('invoice.Reference = periode.Reference AND periode.Deletes = "Jual"');
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
    ->with('invoicess', $invoices)
    ->with('invoicejs', $invoicej)
    ->with('invoicecs', $invoicec)
    ->with('top_menu_sel', 'menu_invoice')
    ->with('page_title', 'Invoice')
    ->with('page_description', 'Index');
    }
/*
    public function create()
    {
    	return view('pages.project.create')
      ->with('page_title', 'Project')
      ->with('page_description', 'Create');
    }

    public function store(Request $request)
    {
    	
    	$inputs = $request->all();

    	$project = Project::Create($inputs);

    	return redirect()->route('project.index');
    }

    public function show($id)
    {
    	$project = Project::find($id);

    	return view('pages.project.show')
      ->with('project', $project)
      ->with('page_title', 'Project')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
    	$project = Project::find($id);

    	return view('pages.project.edit')
      ->with('project', $project)
      ->with('page_title', 'Project')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
    	$project = Project::find($id);

    	$project->PCode = $request->PCode;
    	$project->Project = $request->Project;
      $project->Alamat = $request->Alamat;
    	$project->CCode = $request->CCode;
    	$project->save();

    	return redirect()->route('project.show', $id);
    }

    public function destroy($id)
    {
    	Project::destroy($id);
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('project.index');
    }*/
}
