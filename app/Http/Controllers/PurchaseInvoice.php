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
				$this->access = array("showsewa", "showsewapisah", "showjual", "showjualpisah", "showclaim", "index");
			else
				$this->access = array("");
    return $next($request);
    });
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
			$invoicesk = InvoiceKirim::select([
					'invoicekirim.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicekirim.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 2)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'sjkirim.SJKir', 'isisjkirim.IsiSJKir')
					->from('periode')
					->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
					->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
					->whereRaw('invoicekirim.Reference = periode.Reference AND invoicekirim.SJKir = sjkirim.SJKir')
					->where('periode.Deletes', 'Sewa');
				})
			->groupBy('invoicekirim.Reference', 'invoicekirim.SJKir', 'invoicekirim.Periode')
			->get();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$invoicesk = InvoiceKirim::select([
					'invoicekirim.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicekirim.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 2)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'sjkirim.SJKir', 'isisjkirim.IsiSJKir')
					->from('periode')
					->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
					->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
					->whereRaw('invoicekirim.Reference = periode.Reference AND invoicekirim.SJKir = sjkirim.SJKir')
					->where('periode.Deletes', 'Sewa');
				})
			->where('customer.PPN', 1)
			->groupBy('invoicekirim.Reference', 'invoicekirim.SJKir', 'invoicekirim.Periode')
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$invoicesk = InvoiceKirim::select([
					'invoicekirim.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicekirim.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Sewa')
			->where('pocustomer.INVP', 2)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'sjkirim.SJKir', 'isisjkirim.IsiSJKir')
					->from('periode')
					->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
					->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
					->whereRaw('invoicekirim.Reference = periode.Reference AND invoicekirim.SJKir = sjkirim.SJKir')
					->where('periode.Deletes', 'Sewa');
				})
			->where('customer.PPN', 0)
			->groupBy('invoicekirim.Reference', 'invoicekirim.SJKir', 'invoicekirim.Periode')
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
			$invoicejk = InvoiceKirim::select([
					'invoicekirim.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicekirim.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			->where('pocustomer.INVP', 2)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'sjkirim.SJKir', 'isisjkirim.IsiSJKir')
					->from('periode')
					->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
					->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
					->whereRaw('invoicekirim.Reference = periode.Reference AND invoicekirim.SJKir = sjkirim.SJKir')
					->where('periode.Deletes', 'Jual');
				})
			->groupBy('invoicekirim.Reference', 'invoicekirim.SJKir', 'invoicekirim.Periode')
			->get();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$invoicejk = InvoiceKirim::select([
					'invoicekirim.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicekirim.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			->where('pocustomer.INVP', 2)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'sjkirim.SJKir', 'isisjkirim.IsiSJKir')
					->from('periode')
					->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
					->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
					->whereRaw('invoicekirim.Reference = periode.Reference AND invoicekirim.SJKir = sjkirim.SJKir')
					->where('periode.Deletes', 'Jual');
				})
			->where('customer.PPN', 1)
			->groupBy('invoicekirim.Reference', 'invoicekirim.SJKir', 'invoicekirim.Periode')
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$invoicejk = InvoiceKirim::select([
					'invoicekirim.*',
					'project.Project',
					'customer.Company',
				])
			->leftJoin('transaksi', 'invoicekirim.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('JSC', 'Jual')
			->where('pocustomer.INVP', 2)
			->whereExists(function($query)
				{
					$query->select('periode.Reference', 'sjkirim.SJKir', 'isisjkirim.IsiSJKir')
					->from('periode')
					->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
					->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
					->whereRaw('invoicekirim.Reference = periode.Reference AND invoicekirim.SJKir = sjkirim.SJKir')
					->where('periode.Deletes', 'Jual');
				})
			->where('customer.PPN', 0)
			->groupBy('invoicekirim.Reference', 'invoicekirim.SJKir', 'invoicekirim.Periode')
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
			->with('invoicesks', $invoicesk)
			->with('invoicejs', $invoicej)
			->with('invoicejps', $invoicejp)
			->with('invoicejks', $invoicejk)
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
