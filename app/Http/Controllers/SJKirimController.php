<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Periode;
use App\SJKirim;
use App\IsiSJKirim;
use App\Reference;
use App\Transaksi;
use App\History;
use App\Invoice;
use App\Inventory;
use Session;
use DB;
use Auth;

class SJKirimController extends Controller
{
    public function index()
    {
      $sum = IsiSJKirim::select([
          'isisjkirim.SJKir',
          DB::raw('sum(isisjkirim.QTertanda) AS qttd')
        ])
        ->groupBy('isisjkirim.SJKir');
      $sjkirim = SJKirim::select([
        'qttd',
        'sjkirim.*',
        'project.Project',
        'customer.Customer',
      ])
      ->leftJoin('pocustomer', 'sjkirim.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->leftJoin(DB::raw(sprintf( '(%s) AS T1', $sum->toSql() )), function($join){
          $join->on('T1.SJKir', '=', 'sjkirim.SJKir');
        })
      ->orderBy('sjkirim.id', 'asc')
      ->get();

    	return view('pages.sjkirim.indexs')
      ->with('url', 'sjkirim')
      ->with('sjkirims', $sjkirim)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
      ->with('page_description', 'Index');
    }

    public function create()
    {
      $id = Input::get('id');
      
      $reference = Reference::where('pocustomer.id', $id)
      ->first();
      
      $po = Transaksi::leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
      ->where('transaksi.Reference', $reference->Reference)
      ->first();
      
      $sjkirim = SJKirim::select([
        DB::raw('MAX(sjkirim.id) AS maxid')
      ])
      ->first();
      
    	return view('pages.sjkirim.create')
      ->with('url', 'sjkirim')
      ->with('reference', $reference)
      ->with('po', $po)
      ->with('sjkirim', $sjkirim)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
      ->with('page_description', 'Create');
    }
    
    public function getCreate2(Request $request, $id)
    { 
      Session::put('SJKir', $request->SJKir);
      Session::put('Tgl', $request->Tgl);
      Session::put('JS', $request->JS);
      Session::put('Reference', $request->Reference);
      
      $SJKir = Session::get('SJKir');
      $Tgl = Session::get('Tgl');
      $JS = Session::get('JS');
      $Reference = Session::get('Reference');
      
      $referenceid = Reference::where('pocustomer.id', $id)
      ->first();
      
      $transaksis = Transaksi::select([
        'transaksi.*',
        'project.Project',
      ])
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->where('transaksi.Reference', $Reference)
      ->where('transaksi.JS', $JS)
      ->orderBy('transaksi.id', 'asc')
      ->get();
      
      $isisjkirim = IsiSJKirim::select([
        DB::raw('MAX(isisjkirim.id) AS maxid')
      ])
      ->first();
      
      $sjkirim = SJKirim::select([
        DB::raw('MAX(sjkirim.id) AS maxid')
      ])
      ->first();
      
    	return view('pages.sjkirim.create2')
      ->with('url', 'sjkirim')
      ->with('referenceid', $referenceid)
      ->with('transaksis', $transaksis)
      ->with('isisjkirim', $isisjkirim)
      ->with('sjkirim', $sjkirim)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
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
      
      $SJKir = Session::get('SJKir');
      $Tgl = Session::get('Tgl');
      $Reference = Session::get('Reference');
      
      $referenceid = Reference::where('pocustomer.id', $id)
      ->first();
      
      $transaksis = Transaksi::select([
        'transaksi.*',
        'inventory.Warehouse',
        'project.Project',
      ])
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->where('transaksi.Reference', $Reference)
      ->whereIn('transaksi.Purchase', $Purchase)
      ->orderBy('transaksi.id', 'asc')
      ->get();
      
      $sjkirim = SJKirim::select([
        DB::raw('MAX(sjkirim.id) AS maxid')
      ])
      ->first();
      
      $isisjkirim = IsiSJKirim::select([
        DB::raw('MAX(isisjkirim.id) AS maxid')
      ])
      ->first();
      
      $maxperiode = Periode::select([
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->first();
      
      $maxisisjkir = IsiSJKirim::select([
        DB::raw('MAX(isisjkirim.IsiSJKir) AS IsiSJKir')
      ])
      ->first();
      
      $ECont = Periode::where('Reference', $Reference)
      ->whereRaw('(SELECT MAX(Periode) FROM periode WHERE Reference = ?)', $Reference)
      ->first();
      
      $end = str_replace('/', '-', $Tgl);
      $end2 = strtotime("-1 day +1 month", strtotime($end));
      $end3 = date("d/m/Y", $end2);
      
      if(is_null($ECont)){
        $tglE = $end3;
        $periode = 1;
      }else{
        $tglE = $ECont->E;
        $periode = $ECont->Periode;
      }
      
    	return view('pages.sjkirim.create3')
      ->with('url', 'sjkirim')
      ->with('tglE', $tglE)
      ->with('periode', $periode)
      ->with('referenceid', $referenceid)
      ->with('transaksis', $transaksis)
      ->with('sjkirim', $sjkirim)
      ->with('isisjkirim', $isisjkirim)
      ->with('maxperiode', $maxperiode)
      ->with('maxisisjkir', $maxisisjkir)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
      ->with('page_description', 'Item');
    }
    
    public function store(Request $request)
    {
      $SJKir = Session::get('SJKir');
      $Tgl = Session::get('Tgl');
      $JS = Session::get('JS');
      $Reference = Session::get('Reference');
      
      $sjkirim = SJKirim::Create([
        'id' => $request['sjkirimid'],
        'SJKir' => $SJKir,
        'Tgl' => $Tgl,
        'Reference' => $Reference,
        'NoPolisi' => $request['NoPolisi'],
        'Sopir' => $request['Sopir'],
        'Kenek' => $request['Kenek'],
      ]);
      
      $input = Input::all();
      $isisjkirims = $input['isisjkirimid'];
      foreach ($isisjkirims as $key => $isisjkirim)
      {
        $isisjkirim = new IsiSJKirim;
        $isisjkirim->id = $input['isisjkirimid'][$key];
        $isisjkirim->IsiSJKir = $input['IsiSJKir'][$key];
        $isisjkirim->Warehouse = $input['Warehouse'][$key];
        $isisjkirim->QKirim = $input['QKirim'][$key];
        $isisjkirim->Purchase = $input['Purchase'][$key];
        $isisjkirim->SJKir = $SJKir;
        $isisjkirim->save();
      }
      
      $periodes = $input['isisjkirimid'];
      foreach ($periodes as $key => $periodes)
      {
        $periodes = new Periode;
        $periodes->id = $input['periodeid'][$key];
        $periodes->Periode = $input['Periode'];
        $periodes->S = $Tgl;
        $periodes->E = $input['tglE'];
        $periodes->Quantity = $input['QKirim'][$key];
        $periodes->IsiSJKir = $input['IsiSJKir'][$key];
        $periodes->Reference = $Reference;
        $periodes->Purchase = $input['Purchase'][$key];
        $periodes->Deletes = $input['JS'][$key];
        $periodes->save();
      }
      
      $transaksis = $input['id'];
      foreach ($transaksis as $key => $transaksi)
      {
        $transaksi = Transaksi::find($transaksis[$key]);
        $transaksi->QSisaKirInsert = $input['QSisaKirInsert'][$key]-$input['QKirim'][$key];
        $transaksi->save();
      }
      
      $data = Invoice::where('invoice.Reference', $Reference)
      ->where('invoice.Periode', 1)
      ->where('invoice.JSC', $JS)->first();
      $data->update(['Times' => $data->Times + 1]);
      
      $inventories = $input['ICode'];
      foreach ($inventories as $key => $inventory)
      {
        $data = Inventory::where('Code', $input['ICode'][$key])
        ->first();
        $data->update(['Jumlah' => $data->Jumlah - $input['QKirim'][$key]]);
      }
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Create SJKirim on SJKir '.$SJKir;
      $history->save();
      
      Session::forget('SJKir');
      Session::forget('Tgl');
      Session::forget('JS');
      Session::forget('Reference');
      
    	return redirect()->route('sjkirim.index');
    }

    public function show($id)
    {
      $sjkirim = SJKirim::find($id);

      $isisjkirims = IsiSJKirim::select([
        'isisjkirim.*',
        'periode.Periode',
        'transaksi.*',
        'project.*',
        'customer.*'
      ])
      ->leftJoin('periode', 'isisjkirim.IsiSJKir', '=', 'periode.IsiSJKir')
      ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->groupBy('periode.IsiSJKir')
      ->orderBy('isisjkirim.id', 'asc')
      ->get();
      
      $isisjkirim = $isisjkirims->first();
      
      $jumlah = $isisjkirims->sum('QTertanda');

      $qttdcheck = Periode::select([
        DB::raw('max(periode.Periode) as found')
      ])
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->first();
      if($qttdcheck->found > 1){
        $qttdcheck = 1;
      }else{
        $qttdcheck = 0;
      }
      
    	return view('pages.sjkirim.show')
      ->with('url', 'sjkirim')
      ->with('sjkirim', $sjkirim)
      ->with('isisjkirim', $isisjkirim)
      ->with('isisjkirims', $isisjkirims)
      ->with('jumlah', $jumlah)
      ->with('qttdcheck', $qttdcheck)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
    	$sjkirim = SJKirim::find($id);
      
      $isisjkirims = IsiSJKirim::select([
        'isisjkirim.*',
        'sjkirim.Tgl',
        'transaksi.*',
        'project.Project',
      ])
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->orderBy('isisjkirim.id', 'asc')
      ->get();
      
      $TglMin = Reference::select([
        'pocustomer.Tgl',
      ])
      ->leftJoin('sjkirim', 'pocustomer.Reference', '=', 'sjkirim.Reference')
      ->where('sjkirim.SJKir', $sjkirim->SJKir)
      ->first();
      
    	return view('pages.sjkirim.edit')
      ->with('url', 'sjkirim')
      ->with('sjkirim', $sjkirim)
      ->with('isisjkirims', $isisjkirims)
      ->with('TglMin', $TglMin)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
    	$sjkirim = SJKirim::find($id);
      $sjkirim->Tgl = $request['Tgl'];
      $sjkirim->save();
      
      $isisjkirim = IsiSJKirim::where('isisjkirim.SJKir', $sjkirim -> SJKir);
      $purchase = $isisjkirim->pluck('Purchase');
      $transaksi = Transaksi::whereIn('transaksi.Purchase', $purchase);
      $qkirim = $isisjkirim->pluck('QKirim');
      $icode = $transaksi->pluck('ICode');
      $inventories = $request['Barang'];
      foreach ($inventories as $key => $inventory)
      {
        $data = Inventory::where('Code', $icode[$key])
        ->first();
        $data->update(['Jumlah' => $data->Jumlah + $qkirim[$key] - $request['QKirim'][$key]]);
      }
      
      $input = Input::all();
      $transaksis = $input['id'];
      foreach ($transaksis as $key => $transaksi)
      {
        $transaksi = Transaksi::find($transaksis[$key]);
        $transaksi->QSisaKirInsert = $transaksi->QSisaKirInsert+$input['QKirim2'][$key]-$input['QKirim'][$key];
        $transaksi->save();
      }
      
      $periode = Periode::select('periode.*')
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir);
      $periodeid = $periode->pluck('id');
      
      $periodes = $periodeid;
      foreach ($periodes as $key => $periode)
      {
        $periode = Periode::find($periodes[$key]);
        $periode->Quantity = $input['QKirim'][$key];
        $periode->save();
      }
      
      $isisjkirim = IsiSJKirim::where('isisjkirim.SJKir', $sjkirim->SJKir);
      $isisjkirimid = $isisjkirim->pluck('id');
      
      $isisjkirims = $isisjkirimid;
      foreach ($isisjkirims as $key => $isisjkirim)
      {
        $isisjkirim = IsiSJKirim::find($isisjkirims[$key]);
        $isisjkirim->Warehouse = $input['Warehouse'][$key];
        $isisjkirim->QKirim = $input['QKirim'][$key];
        $isisjkirim->save();
      }
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Update SJKirim on SJKir '.$sjkirim->SJKir;
      $history->save();

    	return redirect()->route('sjkirim.show', $id);
    }
    
    public function getQTertanda($id)
    { 
    	$sjkirim = SJKirim::find($id);
      
      $parameter = IsiSJKirim::select([
        'periode.Periode',
        'periode.Reference',
      ])
      ->leftJoin('periode', 'isisjkirim.IsiSJKir', '=', 'periode.IsiSJKir')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->groupBy('periode.IsiSJKir')
      ->first();
      
      $Tgl = Periode::select([
        'periode.S',
      ])
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->where('periode.Reference', $parameter->Reference)
      ->where('periode.Periode', $parameter->Periode)
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->first();
      
      $periode = Periode::select([
        'periode.id',
        'periode.E',
      ])
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->where('periode.Reference', $parameter->Reference)
      ->where('periode.Periode', $parameter->Periode)
      ->first();
      
      $isisjkirims = IsiSJKirim::select([
        'isisjkirim.*',
        'transaksi.Barang',
        'transaksi.JS',
        'transaksi.QSisaKir',
        'project.Project',
      ])
      ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->orderBy('isisjkirim.id', 'asc')
      ->get();
      
    	return view('pages.sjkirim.qtertanda')
      ->with('url', 'sjkirim')
      ->with('sjkirim', $sjkirim)
      ->with('isisjkirims', $isisjkirims)
      ->with('Tgl', $Tgl)
      ->with('periode', $periode)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
      ->with('page_description', 'QTertanda');
    }
    
    public function postQTertanda(Request $request, $id)
    {
    	$sjkirim = SJKirim::find($id);
      
      $transaksi = Transaksi::select('transaksi.*')
      ->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir);
      $transaksiid = $transaksi->pluck('id');
      
      $input = Input::all();
      $transaksis = $transaksiid;
      foreach ($transaksis as $key => $transaksi)
      {
        $transaksi = Transaksi::find($transaksis[$key]);
        $transaksi->QSisaKir = $transaksi->QSisaKir+$input['QTertanda2'][$key]-$input['QTertanda'][$key];
        $transaksi->QSisaKem = $transaksi->QSisaKem-$input['QTertanda2'][$key]+$input['QTertanda'][$key];
        $transaksi->save();
      }
      Transaksi::where('JS', 'Jual')->update(['QSisaKem' => '0']);
      
      $isisjkirim = IsiSJKirim::where('isisjkirim.SJKir', $sjkirim->SJKir);
      $isisjkirimid = $isisjkirim->pluck('id');
      
      $isisjkirims = $isisjkirimid;
      foreach ($isisjkirims as $key => $isisjkirim)
      {
        $isisjkirim = IsiSJKirim::find($isisjkirims[$key]);
        $isisjkirim->QTertanda = $input['QTertanda'][$key];
        $isisjkirim->QSisaKemInsert = $input['QTertanda'][$key];
        $isisjkirim->QSisaKem = $input['QTertanda'][$key];
        $isisjkirim->save();
      }
      $isisjkirimjualid = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')->where('transaksi.JS', 'Jual')->pluck('isisjkirim.id');
      IsiSJKirim::whereIn('id', $isisjkirimjualid)->update(['QSisaKemInsert' => '0', 'QSisaKem' => '0']);
      
      $periode = Periode::select('periode.*')
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Jual")');
      $periodeid = $periode->pluck('id');
      
      $TglMax = str_replace('/', '-', $input['Tgl']);
      $TglMax2 = strtotime("+1 month -1 day", strtotime($TglMax));
      $TglMax3 = date("d/m/Y", $TglMax2);
      
      if($periode->first()->id == $input['periodeid']){
        $TglMax4 = $TglMax3;
      }else{
        $TglMax4 = $input['E'];
      }
      
      $periodes = $periodeid;
      foreach ($periodes as $key => $periode)
      {
        $periode = Periode::find($periodes[$key]);
        $periode->S = $input['Tgl'];
        $periode->E = $TglMax4;
        $periode->save();
      }

    	return redirect()->route('sjkirim.show', $id);
    }

    public function destroy(Request $request, $id)
    {
      $sjkirim = SJKirim::find($id);
      
      $isisjkirim = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir);
      
      $transaksiid = $isisjkirim->pluck('transaksi.id');
      $qkirim = $isisjkirim->pluck('isisjkirim.QKirim');
      $qtertanda = $isisjkirim->pluck('isisjkirim.QTertanda');
      $qsisakeminsert = $isisjkirim->pluck('isisjkirim.QSisaKemInsert');
      $icode = $isisjkirim->pluck('ICode');
      $JS = $isisjkirim->first()->JS;
      
      $inventories = $icode;
      foreach ($inventories as $key => $inventory)
      {
        $data = Inventory::where('Code', $icode[$key])
        ->first();
        $data->update(['Jumlah' => $data->Jumlah + $qkirim[$key]]);
      }
      
      $data = Invoice::where('invoice.Reference', $sjkirim->Reference)
      ->where('invoice.Periode', 1)
      ->where('invoice.JSC', $JS)->first();
      $data->update(['Times' => $data->Times - 1]);
      
      $transaksis = $transaksiid;
      foreach ($transaksis as $key => $transaksi)
      {
        $transaksi = Transaksi::find($transaksis[$key]);
        $transaksi->QSisaKirInsert = $transaksi->QSisaKirInsert+$qkirim[$key];
        $transaksi->QSisaKir = $transaksi->QSisaKir+$qtertanda[$key];
        $transaksi->QSisaKem = $transaksi->QSisaKem-$qsisakeminsert[$key];
        $transaksi->save();
      }
      
      $periode = Periode::select('periode.*')
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Jual")');
      $periodeid = $periode->pluck('id');
      Periode::whereIn('id', $periodeid)->delete();
      
      IsiSJKirim::where('SJKir', $sjkirim->SJKir)->delete();
      
    	SJKirim::destroy($id);
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Delete SJKirim on SJKir '.$sjkirim->SJKir;
      $history->save();
      
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('sjkirim.index');
    }
}