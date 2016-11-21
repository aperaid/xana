<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Reference;
use App\Transaksi;
use App\PO;
use App\SJKirim;
use App\IsiSJKirim;
use App\SJKembali;
use App\IsiSJKembali;
use App\Periode;
use App\TransaksiClaim;
use App\Invoice;
use Session;
use DB;

class ReferenceController extends Controller
{
    public function index()
    {
    $reference = Reference::select([DB::raw('SUM(transaksi.Amount*transaksi.Quantity) AS Price'), 'pocustomer.*', 'project.*', 'customer.*', 'pocustomer.id as Id'])
            ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
            ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
            ->leftJoin('transaksi', 'pocustomer.Reference', '=', 'transaksi.Reference')
            ->groupBy('pocustomer.Reference')
            ->get();

    	return view('pages.reference.indexs')
      ->with('url', 'reference')
      ->with('reference', $reference)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Index');
    }

    public function create()
    {
      $reference = Reference::orderby('id', 'desc')
      ->first();
      
    	return view('pages.reference.create')
      ->with('url', 'reference')
      ->with('reference', $reference)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Create');
    }

    public function store(Request $request)
    {
    	
    	$inputs = $request->all();

    	$reference = Reference::Create($inputs);

    	return redirect()->route('reference.index');
    }

    public function show($id)
    {
    	$detail = Reference::leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->select('pocustomer.id as pocusid', 'pocustomer.*', 'project.*', 'customer.*')
      ->where('pocustomer.id', $id)
      ->first();
      
      $purchase = Transaksi::leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->where('transaksi.reference', $detail -> Reference)
      ->get();
      
      $sjkircheck = 0;
      $kirexist = Transaksi::where('transaksi.Reference', $detail -> Reference)
      ->count();
      $kirfound = Transaksi::selectRaw('SUM(QSisaKirInsert) as kirfound')
      ->where('transaksi.Reference', $detail -> Reference)
      ->first();
      if($kirexist == 0){
        $sjkircheck = 0;
      }else{
        if($kirfound -> kirfound == 0){
          $sjkircheck = 0;
        }else{
          $sjkircheck = 1;
        }
      }
      
      $sjkemcheck = 0;
      $kemexist = Transaksi::where('transaksi.Reference', $detail -> Reference)
      ->count();
      $kemfound = Transaksi::selectRaw('SUM(QSisaKem) as kemfound')
      ->where('transaksi.Reference', $detail -> Reference)
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
      
      $pocheck = SJKirim::where('sjkirim.Reference', $detail -> Reference)
      ->count();
      if($pocheck == 0){
        $pocheck = 0;
      }else{
        $pocheck = 1;
      }
      
      $periodecheck = Periode::selectRaw('MAX(Periode) as maxper')
      ->where('periode.Reference', $detail -> Reference)
      ->first();
      
      $po = PO::select('po.*')
      ->leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
      ->where('transaksi.Reference', $detail -> Reference)
      ->groupBy('po.POCode')
      ->get();

      $sjkirim = IsiSJKirim::leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->where('sjkirim.Reference', $detail -> Reference)
      ->groupBy('sjkirim.SJKir')
      ->get();
      
      $sjkembali = IsiSJKembali::select([
        'sjkembali.*',
        DB::raw('SUM(isisjkembali.QTerima) AS SumQTerima')
      ])
      ->leftJoin('sjkembali', 'isisjkembali.SJKem', '=', 'sjkembali.SJKem')
      ->where('sjkembali.Reference', $detail -> Reference)
      ->groupBy('sjkembali.SJKem')
      ->get();
      
      $maxid = Periode::select([
        'periode.Reference',
        'periode.IsiSJKir',
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('periode.IsiSJKir')
      ->orderBy('periode.id', 'asc');
      
      $sewa = Periode::select([
        'invoice.id AS invoiceid',
        'invoice.Invoice',
        'periode.*',
        DB::raw('SUM(isisjkirim.QKirim) AS SumQKirim'),
        DB::raw('SUM(isisjkirim.QTertanda) AS SumQTertanda'),
        'maxid'
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
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->where('periode.Reference', $detail -> Reference)
      ->where('invoice.JSC', 'Sewa')
      ->groupBy('invoice.Reference','invoice.Periode')
      ->get();
      
      $jual = Periode::select([
        'pocustomer.Reference',
        'invoice.id',
        'invoice.Invoice',
        'project.Project'
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
      ->where('periode.Deletes', 'Jual')
      ->where('pocustomer.Reference', $detail -> Reference)
      ->where('invoice.JSC', 'Jual')
      ->groupBy('periode.Reference','periode.Periode')
      ->get();
      
      $transaksiclaim = Periode::select([
        'periode.Reference',
        'periode.Claim',
        'periode.Periode',
        DB::raw('MAX(periode.Periode) AS periodeclaim')
      ])
      ->whereRaw('periode.Deletes', 'Claim');
      
      $transaksiextend = Periode::select([
        'periode.Reference',
        DB::raw('MAX(periode.Periode) AS periodeextend')
      ])
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")');
      
      $claim = TransaksiClaim::select([
        'transaksiclaim.*',
        'periodeclaim',
        'periodeextend',
        'invoice.id AS invoiceid',
        'invoice.Invoice',
        'periode.Reference',
        'transaksi.Barang',
        'transaksi.QSisaKem',
        'project.Project',
        'customer.Customer'
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
      ->leftJoin(DB::raw(sprintf( '(%s) AS T1', $transaksiclaim->toSql() )), function($join){
        $join->on('T1.Reference', '=', 'periode.Reference')
        ->on('T1.Claim', '=', 'transaksiclaim.Claim')
        ->on('T1.Periode', '=', 'transaksiclaim.Periode');
      })
      ->leftJoin(DB::raw(sprintf( '(%s) AS T2', $transaksiextend->toSql() )), function($join){
        $join->on('T2.Reference', '=', 'periode.Reference');
      })
      ->where('pocustomer.Reference', $detail -> Reference)
      ->where('invoice.JSC', 'Claim')
      ->groupBy('transaksiclaim.Periode')
      ->orderBy('transaksiclaim.id', 'asc')
      ->get();
      
    	return view('pages.reference.show')
      ->with('url', 'reference')
      ->with('detail', $detail)
      ->with('purchases', $purchase)
      ->with('sjkircheck', $sjkircheck)
      ->with('sjkemcheck', $sjkemcheck)
      ->with('pocheck', $pocheck)
      ->with('periodecheck', $periodecheck)
      ->with('pos', $po)
      ->with('sjkirims', $sjkirim)
      ->with('sjkembalis', $sjkembali)
      ->with('sewas', $sewa)
      ->with('juals', $jual)
      ->with('claims', $claim)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
    	$reference = Reference::find($id);

    	return view('pages.reference.edit')
      ->with('url', 'reference')
      ->with('reference', $reference)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Reference')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
    	$reference = Reference::find($id);

    	$reference->Reference = $request->Reference;
    	$reference->Tgl = $request->Tgl;
      $reference->PCode = $request->PCode;
    	$reference->save();

    	return redirect()->route('reference.show', $id);
    }

    public function destroy($id)
    {
      $reference = Reference::find($id);
      
      $transaksi = Transaksi::where('transaksi.Reference', $reference->Reference);
      $transaksiid = $transaksi->pluck('id');
      $invoice = Invoice::where('Invoice.Reference', $reference->Reference);
      $invoiceid = $invoice->pluck('id');
      $po = PO::whereIn('po.POCode', $transaksi->pluck('POCode'));
      $poid = $po->pluck('id');
      
      Invoice::whereIn('id', $invoiceid)->delete();
      
      Transaksi::whereIn('id', $transaksiid)->delete();
      
      PO::whereIn('id', $poid)->delete();
      
      Reference::destroy($id);

      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('reference.index');
    }
}
