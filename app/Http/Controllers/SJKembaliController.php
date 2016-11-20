<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SJKembali;
use App\IsiSJKembali;
use App\Periode;
use App\Reference;
use App\IsiSJKirim;
use App\Transaksi;
use Session;
Use DB;

class SJKembaliController extends Controller
{
    public function index()
    {
      $sum = IsiSJKembali::select([
          'isisjkembali.SJKem',
          DB::raw('sum(isisjkembali.QTertanda) AS qtrima')
        ])
        ->groupBy('isisjkembali.SJKem');
      $sjkembali = SJKembali::select([
        'qtrima',
        'sjkembali.*',
        'project.Project',
        'customer.Customer',
      ])
      ->leftJoin('pocustomer', 'sjkembali.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->leftJoin(DB::raw(sprintf( '(%s) AS T1', $sum->toSql() )), function($join){
          $join->on('T1.SJKem', '=', 'sjkembali.SJKem');
        })
      ->orderBy('sjkembali.id', 'asc')
      ->get();

    	return view('pages.sjkembali.indexs')
      ->with('url', 'sjkembali')
      ->with('sjkembalis', $sjkembali)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'Index');
    }

    public function create()
    {
    	$id = Input::get('id');
      
      $reference = Reference::where('pocustomer.id', $id)
      ->first();
      
      $sjkembali = SJKembali::select([
        DB::raw('MAX(sjkembali.id) AS maxid')
      ])
      ->first();
      
      $maxperiode = Periode::select([
        DB::raw('MAX(periode.Periode) AS maxperiode')
      ])
      ->where('periode.Reference', $reference->Reference)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->first();
      
      $TglMin = Periode::select('periode.S')
      ->where('periode.Reference', $reference->Reference)
      ->where('periode.Periode', $maxperiode->maxperiode)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->orderBy('periode.id', 'asc')
      ->first();
      
      $TglMax = Periode::select('periode.E')
      ->where('periode.Reference', $reference->Reference)
      ->where('periode.Periode', $maxperiode->maxperiode)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->orderBy('periode.id', 'desc')
      ->first();
      
    	return view('pages.sjkembali.create')
      ->with('url', 'sjkembali')
      ->with('maxperiode', $maxperiode)
      ->with('reference', $reference)
      ->with('sjkembali', $sjkembali)
      ->with('TglMin', $TglMin)
      ->with('TglMax', $TglMax)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'Create');
    }

    public function getCreate2(Request $request, $id)
    { 
      Session::put('SJKem', $request->SJKem);
      Session::put('Tgl', $request->Tgl);
      Session::put('Reference', $request->Reference);
      
      $SJKem = Session::get('SJKem');
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
      
      $isisjkembalis = IsiSJKirim::select([
        'isisjkirim.Purchase',
        DB::raw('SUM(isisjkirim.QSisaKemInsert) AS SumQSisaKemInsert'),
        'periode.S',
        'periode.E',
        'sjkirim.SJKir',
        'sjkirim.Tgl',
        'transaksi.Barang',
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
      
      $isisjkembali = $isisjkembalis->first();

      $tgl = $Tgl;
      $convert = str_replace('/', '-', $tgl);
      $tgls = $isisjkembali->S;
      $converts = str_replace('/', '-', $tgls);
      $tgle = $isisjkembali->E;
      $converte = str_replace('/', '-', $tgle);
      
      $check = strtotime($convert);
      $checks = strtotime($converts);
      $checke = strtotime($converte);
      
    	return view('pages.sjkembali.create2')
      ->with('url', 'sjkembali')
      ->with('id', $id)
      ->with('isisjkembalis', $isisjkembalis)
      ->with('check', $check)
      ->with('checks', $checks)
      ->with('checke', $checke)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'Choose');
    }
    
    public function getCreate3(Request $request, $id)
    {
      $input = Input::only('checkbox');
      $purchases = $input['checkbox'];
      foreach ($purchases as $key => $purchases)
      {
        $Purchase[] = $input['checkbox'][$key];
      }
      
      $SJKem = Session::get('SJKir');
      $Tgl = Session::get('Tgl');
      $Reference = Session::get('Reference');
      
      $sjkembali = SJKembali::select([
        DB::raw('MAX(sjkembali.id) AS maxid')
      ])
      ->first();
      
      $isisjkembali = IsiSJKembali::select([
        DB::raw('MAX(isisjkembali.id) AS maxid')
      ])
      ->first();
      
      $maxperiode = Periode::select([
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->first();
      
      $maxisisjkem = IsiSJKembali::select([
        DB::raw('MAX(isisjkembali.IsiSJKem) AS IsiSJKem')
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
        DB::raw('SUM(isisjkirim.QSisaKemInsert) AS SumQSisaKemInsert'),
        'periode.Periode',
        'periode.S',
        'sjkirim.SJKir',
        'sjkirim.Tgl',
        'transaksi.Barang',
      ])
      ->leftJoin('periode', 'isisjkirim.IsiSJKir', '=', 'periode.IsiSJKir')
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
      ->where('transaksi.Reference', $Reference)
      ->whereIn('isisjkirim.Purchase', $Purchase)
      ->whereIn('periode.id', $maxperiodeid)
      ->groupBy('isisjkirim.Purchase')
      ->orderBy('periode.id', 'asc')
      ->get();
      
    	return view('pages.sjkembali.create3')
      ->with('url', 'sjkembali')
      ->with('id', $id)
      ->with('isisjkirims', $isisjkirims)
      ->with('sjkembali', $sjkembali)
      ->with('isisjkembali', $isisjkembali)
      ->with('maxperiode', $maxperiode)
      ->with('maxisisjkem', $maxisisjkem)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'Item');
    }
    
    public function store(Request $request)
    {
    	$SJKem = Session::get('SJKem');
      $Tgl = Session::get('Tgl');
      $Reference = Session::get('Reference');
      
      $sjkembali = SJKembali::Create([
        'id' => $request['sjkembaliid'],
        'SJKem' => $SJKem,
        'Tgl' => $Tgl,
        'Reference' => $Reference,
      ]);
      
      $maxperiodeid = Periode::select([
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->where('periode.Reference', $Reference)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('periode.IsiSJKir')
      ->orderBy('periode.id', 'desc')
      ->pluck('periode.maxid');
      
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
        $periode->id = $id[$key]+count($periodes);
        $periode->Periode = $input['Periode'];
        $periode->S = $start[$key];
        $periode->E = $Tgl;
        $periode->Quantity = $quantity[$key];
        $periode->SJKem = $SJKem;
        $periode->IsiSJKir = $isisjkir[$key];
        $periode->Reference = $Reference;
        $periode->Purchase = $purchase[$key];
        $periode->Deletes = 'Kembali';
        $periode->save();
      }
      
      $storeprocs = $input['id'];
      foreach ($storeprocs as $key => $storeproc)
      {
        DB::select('CALL insert_sjkembali(?,?,?,?)',array($input['QTertanda'][$key], $input['Purchase'][$key], $input['Periode'][$key], $SJKem));
      }
      
      //DB::select('CALL insert_sjkembali2');
      
      $periode2s = Periode::where('periode.SJKem', $SJKem)
      ->orderBy('periode.id', 'asc')
      ->get();
      $qtertanda = $periode2s->pluck('Quantity');
      
      $isisjkembalis = $id;
      foreach ($isisjkembalis as $key => $isisjkembali)
      {
        $isisjkembali = new IsiSJKembali;
        $isisjkembali->id = $input['isisjkembaliid']+$key;
        $isisjkembali->IsiSJKem = $input['IsiSJKem']+$key;
        $isisjkembali->QTertanda = $qtertanda[$key];
        $isisjkembali->Purchase = $purchase[$key];
        $isisjkembali->SJKem = $SJKem;
        $isisjkembali->Periode = $input['Periode'];
        $isisjkembali->IsiSJKir = $isisjkir[$key];
        $isisjkembali->save();
      }
      
      $isisjkembali2s = $input['Purchase'];
      foreach ($isisjkembali2s as $key => $isisjkembali)
      {
        IsiSJKembali::where('Purchase', $input['Purchase'][$key])
        ->update(['Warehouse' => $input['Warehouse'][$key]]);
      }

      Session::forget('SJKem');
      Session::forget('Tgl');
      Session::forget('Reference');
      
    	return redirect()->route('sjkembali.index');
    }

    public function show($id)
    {
      $sjkembali = SJKembali::select([
        'sjkembali.*',
      ])
      ->leftJoin('pocustomer', 'sjkembali.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->where('sjkembali.id', $id)
      ->first();
      
      $isisjkembalis = IsiSJKembali::select([
        DB::raw('sum(isisjkembali.QTertanda) as SumQTertanda'),
        DB::raw('sum(isisjkembali.QTerima) as SumQTerima'),
        'isisjkembali.*',
        'sjkirim.Tgl',
        'sjkirim.Reference',
        'transaksi.Barang',
        'project.*',
        'customer.*'
      ])
      ->leftJoin('isisjkirim', 'isisjkembali.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'isisjkembali.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->where('isisjkembali.SJKem', $sjkembali->SJKem)
      ->groupBy('isisjkembali.Purchase')
      ->orderBy('isisjkembali.id', 'asc')
      ->get();
      
      $isisjkembali = $isisjkembalis->first();
      
      $qtrimacheck = IsiSJKembali::select([
        DB::raw('sum(isisjkembali.QTerima) as found')
      ])
      ->where('isisjkembali.SJKem', $sjkembali->SJKem)
      ->first();
      if($qtrimacheck->found == 0){
        $qtrimacheck = 0;
      }else{
        $qtrimacheck = 1;
      }
      
    	return view('pages.sjkembali.show')
      ->with('url', 'sjkembali')
      ->with('sjkembali', $sjkembali)
      ->with('isisjkembali', $isisjkembali)
      ->with('isisjkembalis', $isisjkembalis)
      ->with('qtrimacheck', $qtrimacheck)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
      $sjkembali = SJKembali::find($id);
      
      $TglMin = Periode::select([
        'periode.S',
      ])
      ->where('periode.Reference', $sjkembali->Reference)
      ->where('periode.Deletes', 'Sewa')
      ->first();
      
      $TglMax = Periode::select([
        'periode.E',
      ])
      ->where('periode.Reference', $sjkembali->Reference)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->orderBy('Periode.id', 'desc')
      ->first();
      
      $isisjkembalis = IsiSJKembali::select([
        DB::raw('sum(isisjkembali.QTertanda) as SumQTertanda'),
        'isisjkembali.*',
        'sjkirim.Tgl',
        'transaksi.Barang',
        'transaksi.QSisaKem',
        'project.Project',
      ])
      ->leftJoin('isisjkirim', 'isisjkembali.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'isisjkembali.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->where('isisjkembali.SJKem', $sjkembali->SJKem)
      ->groupBy('isisjkembali.Purchase')
      ->orderBy('isisjkembali.id', 'asc')
      ->get();

    	return view('pages.sjkembali.edit')
      ->with('url', 'sjkembali')
      ->with('sjkembali', $sjkembali)
      ->with('TglMin', $TglMin)
      ->with('TglMax', $TglMax)
      ->with('isisjkembalis', $isisjkembalis)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
    	$sjkembali = SJKembali::find($id);
      
      $isisjkembali = IsiSJKembali::where('isisjkembali.SJKem', $sjkembali->SJKem);
      $isisjkir = $isisjkembali->pluck('IsiSJKir');
      $qtertanda = $isisjkembali->pluck('QTertanda');
      $maxperiode = $isisjkembali->select([DB::raw('max(isisjkembali.Periode) as maxperiode')])->first();

      $isisjkirims = $isisjkir;
      foreach ($isisjkirims as $key => $isisjkirim)
      {
        $data = IsiSJKirim::where('IsiSJKir', $isisjkir[$key])->first();
        $data->update(['QSisaKemInsert' => $data->QSisaKemInsert + $qtertanda[$key]]);
      }

      $isisjkembalis = $isisjkir;
      foreach ($isisjkembalis as $key => $isisjkembali)
      {
        IsiSJKembali::where('IsiSJKir', $isisjkir[$key])
        ->where('SJKem', $sjkembali->SJKem)
        ->update(['QTertanda' => 0]);
      }
      
      $input = Input::all();
      $isisjkembali2s = $input['Purchase'];
      foreach ($isisjkembali2s as $key => $isisjkembali)
      {
        IsiSJKembali::where('Purchase', $input['Purchase'][$key])
        ->where('SJKem', $sjkembali->SJKem)
        ->update(['Warehouse' => $input['Warehouse'][$key]]);
      }
      
      $storeprocs = $input['id'];
      foreach ($storeprocs as $key => $storeproc)
      {
        DB::select('CALL edit_sjkembali(?,?,?)',array($input['QTertanda'][$key], $input['Purchase'][$key], $sjkembali->SJKem));
      }

      $isisjkirim = IsiSJKirim::whereIn('isisjkirim.IsiSJKir', $isisjkir);
      $qsisakeminsert = $isisjkirim->pluck('QSisaKemInsert');
      $periodes = $isisjkir;
      foreach ($periodes as $key => $periode)
      {
        $data = Periode::where('periode.Periode', $maxperiode->maxperiode)
        ->where('periode.IsiSJKir', $isisjkir[$key])
        ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')->first();
        $data->update(['Quantity' => $qsisakeminsert[$key]]);
      }

      $isisjkembali2 = IsiSJKembali::where('isisjkembali.SJKem', $sjkembali->SJKem);
      $qtertanda2 = $isisjkembali2->pluck('QTertanda');
      $periode2s = $isisjkir;
      foreach ($periode2s as $key => $periode)
      {
        $data = Periode::where('periode.SJKem', $sjkembali->SJKem)
        ->where('periode.IsiSJKir', $isisjkir[$key])
        ->where('periode.Deletes', 'Kembali')->first();
        $data->update(['Quantity' => $qtertanda2[$key]]);
      }
      
      SJKembali::where('sjkembali.id', $id)
      ->update(['sjkembali.Tgl' => $input['Tgl2']]);

    	return redirect()->route('sjkembali.show', $id);
    }
  
    public function getQTerima($id)
    { 
    	$sjkembali = SJKembali::find($id);
      
      $isisjkembalis = IsiSJKembali::select([
        DB::raw('sum(isisjkembali.QTertanda) as SumQTertanda'),
        DB::raw('sum(isisjkembali.QTerima) as SumQTerima'),
        'isisjkembali.*',
        'isisjkirim.QSisaKem',
        'sjkirim.Tgl',
        'transaksi.Barang',
        'project.Project',
      ])
      ->leftJoin('isisjkirim', 'isisjkembali.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'isisjkembali.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->where('isisjkembali.SJKem', $sjkembali->SJKem)
      ->groupBy('isisjkembali.Purchase')
      ->orderBy('isisjkembali.id', 'asc')
      ->get();
      
      $isisjkembali = IsiSJKembali::select([
        'isisjkembali.*',
      ])
      ->leftJoin('isisjkirim', 'isisjkembali.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'isisjkembali.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->where('isisjkembali.SJKem', $sjkembali->SJKem)
      ->orderBy('isisjkembali.id', 'asc')
      ->get();
      
      $QTerima2 = $isisjkembali->pluck('QTerima');
      $IsiSJKir = $isisjkembali->pluck('IsiSJKir');
      
      $Tgl = Periode::select([
        'Periode.E',
      ])
      ->whereIn('periode.IsiSJKir', $IsiSJKir)
      ->where('periode.Deletes', 'Kembali')
      ->first();
      
    	return view('pages.sjkembali.qterima')
      ->with('url', 'sjkembali')
      ->with('sjkembali', $sjkembali)
      ->with('isisjkembalis', $isisjkembalis)
      ->with('QTerima2', $QTerima2)
      ->with('Tgl', $Tgl)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'QTerima');
    }
    
    public function postQTerima(Request $request, $id)
    {
    	$sjkembali = SJKembali::find($id);
      
      $isisjkembali = IsiSJKembali::where('isisjkembali.SJKem', $sjkembali->SJKem);
      $isisjkir = $isisjkembali->pluck('IsiSJKir');
      $qterima = $isisjkembali->pluck('QTerima');
      
      $input = Input::all();
      $transaksis = $input['Purchase'];
      foreach ($transaksis as $key => $transaksi)
      {
        $data = Transaksi::where('Purchase', $input['Purchase'][$key])->first();
        $data->update(['QSisaKem' => $data->QSisaKem + $input['QTerima2'][$key] - $input['QTerima'][$key]]);
      }
      
      $isisjkirims = $isisjkir;
      foreach ($isisjkirims as $key => $isisjkirim)
      {
        $data = IsiSJKirim::where('IsiSJKir', $isisjkir[$key])->first();
        $data->update(['QSisaKem' => $data->QSisaKem + $qterima[$key]]);
      }
      
      $isisjkembalis = $isisjkir;
      foreach ($isisjkembalis as $key => $isisjkembali)
      {
        $data = IsiSJKembali::where('IsiSJKir', $isisjkir[$key])->first();
        $data->update(['QTerima' => 0]);
      }
      
      $storeprocs = $input['id'];
      foreach ($storeprocs as $key => $storeproc)
      {
        DB::select('CALL edit_sjkembaliquantity(?,?,?,?)',array($input['QTerima'][$key], $input['Purchase'][$key], $sjkembali->SJKem, $isisjkir[$key]));
      }

      $periodes = $isisjkir;
      foreach ($periodes as $key => $periode)
      {
        $data = Periode::where('IsiSJKir', $isisjkir[$key])
        ->where('Deletes', 'Kembali')
        ->where('SJKem', $sjkembali->SJKem)
        ->first();
        $data->update(['E' => $input['Tgl2']]);
      }

    	return redirect()->route('sjkembali.show', $id);
    }

    public function destroy($id)
    {
      $sjkembali = SJKembali::find($id);
      
      $periode = Periode::where('periode.SJKem', $sjkembali->SJKem);
      $quantity = $periode->pluck('Quantity');
      $isisjkembali = IsiSJKembali::where('isisjkembali.SJKem', $sjkembali->SJKem);
      $isisjkir = $isisjkembali->pluck('IsiSJKir');
      $qterima = $isisjkembali->pluck('QTerima');
      $qtertanda = $isisjkembali->pluck('QTertanda');
      $sumqtertanda = $isisjkembali->select([DB::raw('sum(isisjkembali.QTertanda) AS SumQTertanda'), 'isisjkembali.Purchase'])->groupBy('isisjkembali.Purchase')->get();
      $maxperiode = $isisjkembali->select([DB::raw('max(isisjkembali.Periode) as maxperiode')])->first();
      
      $transaksis = $sumqtertanda->pluck('Purchase');
      foreach ($transaksis as $key => $transaksi)
      {
        $data = Transaksi::where('Purchase', $sumqtertanda->pluck('Purchase')[$key])->first();
        $data->update(['QSisaKem' => $data->QSisaKem + $sumqtertanda->pluck('SumQTertanda')[$key]]);
      }
      
      $isisjkirims = $isisjkir;
      foreach ($isisjkirims as $key => $isisjkirim)
      {
        $data = IsiSJKirim::where('IsiSJKir', $isisjkir[$key])->first();
        $data->update(['QSisaKemInsert' => $data->QSisaKemInsert + $qtertanda[$key], 'QSisaKem' => $data->QSisaKem + $qterima[$key]]);
      }

      $periodes = $isisjkir;
      foreach ($periodes as $key => $periode)
      {
        $data = Periode::where('Periode', $maxperiode->maxperiode)
        ->where('IsiSJKir', $isisjkir[$key])
        ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')->first();
        $data->update(['Quantity' => $data->Quantity + $quantity[$key]]);
      }
      
      Periode::where('SJKem', $sjkembali->SJKem)->delete();
      
      IsiSJKembali::where('SJKem', $sjkembali->SJKem)->delete();
      
    	SJKembali::destroy($id);
      
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('sjkembali.index');
    }
}
