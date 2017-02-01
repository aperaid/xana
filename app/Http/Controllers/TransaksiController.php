<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;
use App\Periode;
use App\Invoice;
use App\TransaksiClaim;
use App\Reference;
use App\IsiSJKirim;
use App\Transaksi;
use App\History;
use Session;
use DB;
use Auth;

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
        DB::raw('SUM(isisjkirim.QKirim) AS SumQKirim'),
        DB::raw('SUM(isisjkirim.QTertanda) AS SumQTertanda'),
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
        'transaksi.Reference',
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
      
      if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='POPPN'||Auth::user()->access()=='PONONPPN'){
        return view('pages.transaksi.indexs')
        ->with('url', 'transaksi')
        ->with('transaksiss', $transaksis)
        ->with('transaksijs', $transaksij)
        ->with('transaksics', $transaksic)
        ->with('top_menu_sel', 'menu_transaksi')
        ->with('page_title', 'Transaksi')
        ->with('page_description', 'Index');
      }else
        return redirect()->back();
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
      $quantity = $periodes->pluck('periode.Quantity');
      $isisjkir = $periodes->pluck('periode.IsiSJKir');
      $purchase = $periodes->pluck('periode.Purchase');
      
      $periodeid = Periode::select([DB::raw('max(periode.id) as maxid')])->first();
      
      $projectcode = Reference::where('Reference', $invoice->Reference)->first();
      
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
      $Count = $invoice->Count+1;
      $Periode = $invoice->Periode+1;
      
      if(substr($invoice->Tgl,6)!=date('Y')){
        Invoice::Create([
          'id' => $maxinvoice->maxinvoice+1,
          'Invoice' => $projectcode->PCode.str_pad($Periode, 2, "0", STR_PAD_LEFT)."/1/".substr($invoice->Tgl, 3, -5).substr($invoice->Tgl, 6)."/BDN",
          'JSC' => 'Sewa',
          'Tgl' => $TglInvoice2,
          'Reference' => $invoice->Reference,
          'Periode' => $Periode,
          'PPN' => $invoice->PPN,
          'Count' => 1,
        ]);
      }else{
        Invoice::Create([
          'id' => $maxinvoice->maxinvoice+1,
          'Invoice' =>  $projectcode->PCode.str_pad($Periode, 2, "0", STR_PAD_LEFT)."/".$Count."/".substr($invoice->Tgl, 3, -5).substr($invoice->Tgl, 6)."/BDN",
          'JSC' => 'Sewa',
          'Tgl' => $TglInvoice2,
          'Reference' => $invoice->Reference,
          'Periode' => $Periode,
          'PPN' => $invoice->PPN,
          'Count' => $Count,
        ]);
      }

      $periode = $purchase;
      foreach ($periode as $key => $periode)
      {
        $periode = new Periode;
        $periode->id = $periodeid->maxid + $key + 1;
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
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Extend Transaksi Sewa on Invoice '.str_pad($maxinvoice->maxinvoice + 1, 5, "0", STR_PAD_LEFT);
      $history->save();
      
      Session::flash('message', 'Extend is successful!');

    	return redirect()->route('transaksi.index');
    }
    
    public function getClaim($id)
    {
      $reference = Reference::find($id);
      
      $maxperiode = Periode::select([DB::raw('max(periode.Periode) as maxperiode')])
      ->where('periode.Reference', $reference->Reference)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->first();
      
      $TglMin = Periode::select('S')
      ->where('periode.Reference', $reference->Reference)
      ->where('periode.Periode', $maxperiode->maxperiode)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->orderBy('periode.id', 'asc')
      ->first();
      
      $TglMax = Periode::select('E')
      ->where('periode.Reference', $reference->Reference)
      ->where('periode.Periode', $maxperiode->maxperiode)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->orderBy('periode.id', 'desc')
      ->first();
      
      if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='POPPN'||Auth::user()->access()=='PONONPPN'){
        return view('pages.transaksi.claimcreate')
        ->with('url', 'transaksi')
        ->with('reference', $reference)
        ->with('TglMin', $TglMin)
        ->with('TglMax', $TglMax)
        ->with('top_menu_sel', 'menu_transaksi')
        ->with('page_title', 'Transaksi Claim')
        ->with('page_description', 'Create');
      }else
        return redirect()->back();
    }
    
    public function getClaim2(Request $request, $id)
    {
      Session::put('Tgl', $request->Tgl);
      Session::put('Reference', $request->Reference);
      
      $Tgl = Session::get('Tgl');
      $Reference = Session::get('Reference');
      
      $maxperiodeid = Periode::select([
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->where('periode.Reference', $Reference)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('periode.IsiSJKir')
      ->orderBy('periode.id', 'asc')
      ->pluck('periode.maxid');
      
      $isisjkirims = IsiSJKirim::select([
        'isisjkirim.*',
        DB::raw('SUM(isisjkirim.QSisaKem) AS SumQSisaKem'),
        'periode.S',
        'periode.E',
        'transaksi.Barang',
        'transaksi.JS',
      ])
      ->leftJoin('periode', 'isisjkirim.IsiSJKir', '=', 'periode.IsiSJKir')
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
      ->where('sjkirim.Reference', $Reference)
      ->where('transaksi.JS', 'Sewa')
      ->whereIn('periode.id', $maxperiodeid)
      ->groupBy('isisjkirim.Purchase')
      ->orderBy('periode.id', 'asc')
      ->get();
      
      foreach($isisjkirims as $isisjkirim){
        $convert = str_replace('/', '-', $Tgl);
        $tgls = $isisjkirim->S;
        $converts = str_replace('/', '-', $tgls);
        $tgle = $isisjkirim->E;
        $converte = str_replace('/', '-', $tgle);
        
        $check = strtotime($convert);
        $checks = strtotime($converts);
        $checke = strtotime($converte);
      }
      
      if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='POPPN'||Auth::user()->access()=='PONONPPN'){
        return view('pages.transaksi.claimcreate2')
        ->with('url', 'transaksi')
        ->with('id', $id)
        ->with('isisjkirims', $isisjkirims)
        ->with('check', $check)
        ->with('checks', $checks)
        ->with('checke', $checke)
        ->with('top_menu_sel', 'menu_transaksi')
        ->with('page_title', 'Transaksi Claim')
        ->with('page_description', 'Choose');
      }else
        return redirect()->back();
    }
    
    public function getClaim3(Request $request, $id)
    {
      $input = Input::only('checkbox');
      $purchases = $input['checkbox'];
      foreach ($purchases as $key => $purchases)
      {
        $Purchase[] = $input['checkbox'][$key];
      }
      
      $Tgl = Session::get('Tgl');
      $Reference = Session::get('Reference');
      
      $invoice = Invoice::select([
        DB::raw('MAX(invoice.id) AS maxid')
      ])
      ->first();
      
      $claim = TransaksiClaim::select([
        DB::raw('MAX(transaksiclaim.id) AS maxid')
      ])
      ->first();

      $maxperiodeid = Periode::select([
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->where('periode.Reference', $Reference)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('periode.IsiSJKir')
      ->orderBy('periode.id', 'desc')
      ->pluck('periode.maxid');
      
      $isisjkirims = IsiSJKirim::select([
        'isisjkirim.*',
        DB::raw('SUM(isisjkirim.QSisaKem) AS SumQSisaKem'),
        'periode.Periode',
        'periode.S',
        'periode.E',
        'transaksi.Barang',
        'transaksi.POCode',
        'inventory.JualPrice',
      ])
      ->leftJoin('periode', 'isisjkirim.IsiSJKir', '=', 'periode.IsiSJKir')
      ->leftJoin('transaksi', 'periode.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
      ->where('transaksi.Reference', $Reference)
      ->whereIn('isisjkirim.Purchase', $Purchase)
      ->whereIn('periode.id', $maxperiodeid)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('isisjkirim.Purchase')
      ->orderBy('periode.id', 'asc')
      ->get();
      
      if(Auth::check()&&Auth::user()->access()=='Admin'||Auth::user()->access()=='POPPN'||Auth::user()->access()=='PONONPPN'){
        return view('pages.transaksi.claimcreate3')
        ->with('url', 'transaksi')
        ->with('id', $id)
        ->with('invoice', $invoice)
        ->with('claim', $claim)
        ->with('isisjkirims', $isisjkirims)
        ->with('top_menu_sel', 'menu_transaksi')
        ->with('page_title', 'Transaksi Claim')
        ->with('page_description', 'Item');
      }else
        return redirect()->back();
    }

    public function postClaim(Request $request)
    {
      $Tgl = Session::get('Tgl');
      $Reference = Session::get('Reference');
      
      $projectcode = Reference::where('Reference', $Reference)->first();
      
      $invoice = Invoice::Create([
        'id' => $request['invoiceid'],
        'Invoice' => $projectcode->PCode."/".$request->Periode."CL/".substr($Tgl, 3, -5).substr($Tgl, 6)."/BDN",
        'JSC' => 'Claim',
        'Tgl' => $Tgl,
        'Reference' => $Reference,
        'Periode' => $request['Periode'],
        'PPN' => $request['PPN'],
      ]);
      
      $maxperiodeid = Periode::select([
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->where('periode.Reference', $Reference)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('periode.IsiSJKir')
      ->orderBy('periode.id', 'desc')
      ->pluck('periode.maxid');
      
      $maxperiode = Periode::select([
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->first();
      
      $periodes = Periode::whereIn('periode.id', $maxperiodeid)
      ->where('periode.Reference', $Reference)
      ->orderBy('periode.id', 'asc')
      ->get();
      $id = $periodes->pluck('id');
      $start = $periodes->pluck('S');
      $quantity = $periodes->pluck('Quantity');
      $isisjkir = $periodes->pluck('IsiSJKir');
      $purchase = $periodes->pluck('Purchase');
      
      $input = Input::all();
      $periodes = $id;
      foreach ($periodes as $key => $periode)
      {
        $periode = new Periode;
        $periode->id = $maxperiode->maxid+$key+1;
        $periode->Periode = $input['Periode'];
        $periode->S = $start[$key];
        $periode->E = $Tgl;
        $periode->Quantity = $quantity[$key];
        $periode->IsiSJKir = $isisjkir[$key];
        $periode->Reference = $Reference;
        $periode->Purchase = $purchase[$key];
        $periode->Deletes = 'Claim';
        $periode->save();
      }
      
      $transaksis = $input['Purchase'];
      foreach ($transaksis as $key => $transaksi)
      {
        $data = Transaksi::where('Purchase', $input['Purchase'][$key])->first();
        $data->update(['QSisaKem' => $data->QSisaKem - $input['QClaim'][$key]]);
      }
      
      $storeprocs = $input['id'];
      foreach ($storeprocs as $key => $storeproc)
      {
        DB::select('CALL insert_claim(?,?,?,?)',array($input['QClaim'][$key], $input['Purchase'][$key], $input['Periode'], $input['claim'][$key]));
      }
      
      DB::select('CALL insert_claim2');
      
      $claims = $input['id'];
      foreach ($claims as $key => $claim)
      {
        $claim = new TransaksiClaim;
        $claim->id = $input['claim'][$key];
        $claim->Claim = $input['claim'][$key];
        $claim->Tgl = $Tgl;
        $claim->QClaim = $input['QClaim'][$key];
        $claim->Amount = str_replace(".","",substr($input['Amount'][$key], 3));
        $claim->Purchase = $input['Purchase'][$key];
        $claim->Periode = $input['Periode'];
        $claim->IsiSJKir = $input['IsiSJKir'][$key];
        //$claim->PPN = $input['PPN'];
        $claim->save();
      }
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Create Transaksi Claim on claim '.str_pad($request['invoiceid'], 5, "0", STR_PAD_LEFT);
      $history->save();

      Session::forget('Tgl');
      Session::forget('Reference');
      
    	return redirect()->route('transaksi.index');
    }
    
    public function getClaimDelete(Request $request, $id)
    {
      return view('pages.transaksi.claimdelete')
      ->with('id', $id);
    }
    
    public function postClaimDelete(Request $request, $id)
    {
      $invoice = Invoice::find($id);
      
      $periodes = Periode::select([
        DB::raw('SUM(periode.Quantity) AS SumQuantity'),
        'periode.*',
      ])
      ->where('periode.Reference', $invoice->Reference)
      ->where('periode.Periode', $invoice->Periode)
      ->where('periode.Deletes', 'Claim')
      ->groupBy('periode.IsiSJKir');
      $periodeid = $periodes->pluck('periode.id');
      $quantity = $periodes->pluck('periode.Quantity');
      $claim = $periodes->pluck('periode.Claim');
      $purchase = $periodes->pluck('periode.Purchase');
      $isisjkir = $periodes->pluck('periode.IsiSJKir');
      
      $transaksis = $purchase;
      foreach ($transaksis as $key => $transaksi)
      {
        $data = Transaksi::where('Reference', $invoice->Reference)->where('Purchase', $purchase[$key])->first();
        $data->update(['QSisaKem' => $data->QSisaKem + $quantity[$key]]);
      }
      
      $isisjkirims = $purchase;
      foreach ($isisjkirims as $key => $isisjkirim)
      {
        $data = IsiSJKirim::where('IsiSJKir', $isisjkir[$key])->where('Purchase', $purchase[$key])->first();
        $data->update(['QSisaKemInsert' => $data->QSisaKemInsert + $quantity[$key]]);
        $data->update(['QSisaKem' => $data->QSisaKem + $quantity[$key]]);
      }
      
      $periodes = $purchase;
      foreach ($periodes as $key => $periode)
      {
        $data = Periode::where('IsiSJKir', $isisjkir[$key])->where('Purchase', $purchase[$key])->where('Periode', $invoice->Periode)->whereRaw('(Deletes = "Sewa" OR Deletes = "Extend")')->first();
        $data->update(['Quantity' => $data->Quantity + $quantity[$key]]);
      }
      
      Periode::where('Reference', $invoice->Reference)->whereIn('Purchase', $purchase)->whereIn('IsiSJKir', $isisjkir)->whereIn('Claim', $claim)->where('Deletes', 'Claim')->delete();
      
      TransaksiClaim::whereIn('Claim', $claim)->delete();
      
      Invoice::destroy($id);
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Delete Transaksi Claim on claim '.$invoice->Invoice;
      $history->save();
      
      Session::flash('message', 'Delete claim is successful!');

    	return redirect()->route('transaksi.index');
    }
}
