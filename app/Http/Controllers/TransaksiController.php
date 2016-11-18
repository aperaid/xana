<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Periode;
use App\Invoice;
use App\TransaksiClaim;
use App\Reference;
use Session;
use DB;

class TransaksiController extends Controller
{
    public function index()
    {
      $maxid = Periode::select([
        'periode.Reference',
        'periode.IsiSJKir',
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('periode.IsiSJKir')
      ->orderBy('periode.id', 'asc');
      
      $transaksis = Periode::select([
        'invoice.id AS invoiceid',
        'invoice.Invoice',
        'periode.id AS periodeid',
        'periode.*',
        'isisjkirim.SJKir',
        'project.Project',
        'customer.Customer',
        'maxid',
      ])
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->leftJoin('pocustomer', 'periode.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->leftJoin('invoice', function($join){
        $join->on('invoice.Reference', '=', 'pocustomer.Reference')
        ->on('invoice.Periode', '=', 'periode.Periode');
      })
      ->leftJoin(DB::raw(sprintf( '(%s) AS T1', $maxid->toSql() )), function($join){
        $join->on('T1.Reference', '=', 'periode.Reference')
        ->on('T1.IsiSJKir', '=', 'periode.IsiSJKir');
      })
      ->where('invoice.JSC', 'Sewa')
      ->whereRaw('periode.Deletes = "Sewa" OR periode.Deletes = "Extend"')
      ->groupBy('invoice.Reference', 'invoice.Periode')
      ->get();
      
      $transaksij = Periode::select([
        'invoice.id',
        'invoice.Invoice',
        'pocustomer.Reference',
        'project.Project',
      ])
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'sjkirim.Reference', '=', 'transaksi.Reference')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->leftJoin('invoice', function($join){
        $join->on('invoice.Reference', '=', 'transaksi.Reference')
        ->on('invoice.Periode', '=', 'periode.Periode');
      })
      ->where('invoice.JSC', 'Jual')
      ->where('periode.Deletes', 'Jual')
      ->groupBy('periode.Reference', 'periode.Periode')
      ->get();
      
      $T1 = Periode::select([
        'periode.Reference',
        'periode.Claim',
        'periode.Periode',
        DB::raw('MAX(periode.Periode) AS periodeclaim')
      ])
      ->whereRaw('periode.Deletes = "Claim"');
      
      $T2 = Periode::select([
        'periode.Reference',
        DB::raw('MAX(periode.Periode) AS periodeextend')
      ])
      ->whereRaw('periode.Deletes = "Sewa" OR periode.Deletes = "Extend"');
      
      $transaksic = TransaksiClaim::select([
        'periodeclaim',
        'periodeextend',
        'transaksiclaim.*',
        'invoice.id AS invoiceid',
        'invoice.Invoice',
        'periode.Reference',
        'transaksi.Barang',
        'transaksi.QSisaKem',
        'project.Project',
        'customer.Customer',
      ])
      ->leftJoin('periode', 'transaksiclaim.Claim', '=', 'periode.Claim')
      ->leftJoin('transaksi', 'transaksiclaim.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->leftJoin('invoice', function($join){
        $join->on('invoice.Reference', '=', 'transaksi.Reference')
        ->on('invoice.Periode', '=', 'transaksiclaim.Periode');
      })
      ->leftJoin(DB::raw(sprintf( '(%s) AS T1', $T1->toSql() )), function($join){
        $join->on('T1.Reference', '=', 'periode.Reference')
        ->on('T1.Claim', '=', 'transaksiclaim.Claim')
        ->on('T1.Periode', '=', 'transaksiclaim.Periode');
      })
      ->leftJoin(DB::raw(sprintf( '(%s) AS T2', $T2->toSql() )), function($join){
        $join->on('T2.Reference', '=', 'periode.Reference');
      })
      ->where('invoice.JSC', 'Claim')
      ->groupBy('transaksiclaim.Periode')
      ->orderBy('transaksiclaim.id', 'asc')
      ->get();
      
      return view('pages.transaksi.indexs')
      ->with('url', 'transaksi')
      ->with('transaksiss', $transaksis)
      ->with('transaksijs', $transaksij)
      ->with('transaksics', $transaksic)
      ->with('top_menu_sel', 'menu_transaksi')
      ->with('page_title', 'Transaksi')
      ->with('page_description', 'Index');
    }

    public function getExtend($id)
    {
    	return view('pages.transaksi.extend')
      ->with('id', $id);
    }
    
    public function postExtend(Request $request, $id)
    {
      $invoice = Invoice::find($id);
      $maxinvoice = Invoice::select([DB::raw('max(invoice.id) as maxinvoice')])->first();
      
      $periodes = Periode::leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->where('periode.Reference', $invoice->Reference)
      ->where('periode.Periode', $invoice->Periode)
      ->whereNull('periode.SJKem')
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")');
      $periodeid = $periodes->pluck('periode.id');
      $quantity = $periodes->pluck('periode.Quantity');
      $isisjkir = $periodes->pluck('periode.IsiSJKir');
      $purchase = $periodes->pluck('periode.Purchase');
      
      $Tgl = $invoice->Tgl;
      $Tgl2 = str_replace('/', '-', $Tgl);
      $TglInvoice = strtotime("+1 month", strtotime($Tgl2));
      $TglInvoice2 = date("d/m/Y", $TglInvoice);
      $E = $periodes->first()->E;
      $E2 = str_replace('/', '-', $E);
      $SPeriode = strtotime("+1 day", strtotime($E2));
      $SPeriode2 = date("d/m/Y", $SPeriode);
      $EPeriode = strtotime("+1 month", strtotime($E2));
      $EPeriode2 = date("d/m/Y", $EPeriode);
      
      Invoice::Create([
        'id' => $maxinvoice->maxinvoice+1,
        'Invoice' => str_pad($maxinvoice->maxinvoice + 1, 5, "0", STR_PAD_LEFT),
        'JSC' => 'Sewa',
        'Tgl' => $TglInvoice2,
        'Reference' => $invoice->Reference,
        'Periode' => $invoice->Periode+1,
        'PPN' => $invoice->PPN,
        'POCode' => $invoice->POCode,
      ]);

      $periode = $periodeid;
      foreach ($periode as $key => $periode)
      {
        $periode = new Periode;
        $periode->id = $periodeid[$key]+count($periodeid);
        $periode->Periode = $periodes->first()->Periode+1;
        $periode->S = $SPeriode2;
        $periode->E = $EPeriode2;
        $periode->Quantity = $quantity[$key];
        $periode->IsiSJKir = $isisjkir[$key];
        $periode->Reference = $periodes->first()->Reference;
        $periode->Purchase = $purchase[$key];
        $periode->Claim = '';
        $periode->Deletes = 'Extend';
        $periode->save();
      }
      
      Session::flash('message', 'Extend is successful!');

    	return redirect()->route('transaksi.index');
    }
    
    public function getClaim($id)
    {
      $reference = Reference::find($id);
      
    	return view('pages.po.create')
      ->with('url', 'po')
      ->with('po', $po)
      ->with('transaksi', $transaksi)
      ->with('id', $id)
      ->with('reference', $reference)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Item');
    }
}
