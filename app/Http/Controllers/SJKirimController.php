<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PO;
use App\Periode;
use App\SJKirim;
use App\IsiSJKirim;
use App\Reference;
use App\Transaksi;
use App\History;
use App\Invoice;
use App\InvoicePisah;
use App\Inventory;
use Session;
use DB;
use Auth;

class SJKirimController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access == 'Admin'||Auth::user()->access()=='CUSTINVPPN'||Auth::user()->access()=='CUSTINVNONPPN'))
				$this->access = array("index", "create", "create2", "create3", "show", "edit", "qtertanda");
			else if(Auth::check()&&(Auth::user()->access()=='POINVPPN'||Auth::user()->access()=='POINVNONPPN'))
				$this->access = array("index", "create", "create2", "create3", "show", "edit", "qtertanda");
			else
				$this->access = array("");
			
			if(Auth::user()->access()=='POINVPPN'||Auth::user()->access()=='CUSTINVPPN')
				$this->PPNNONPPN = 1;
			else if(Auth::user()->access()=='POINVNONPPN'||Auth::user()->access()=='CUSTINVNONPPN')
				$this->PPNNONPPN = 0;
    return $next($request);
    });
	}
	
	public function index()
	{
		$sum = IsiSJKirim::select([
				'isisjkirim.SJKir',
				DB::raw('sum(isisjkirim.QTertanda) AS qttd')
			])
			->groupBy('isisjkirim.SJKir');
			
		if(Auth::user()->access == 'Admin'){
			$sjkirim = SJKirim::select([
				'qttd',
				'sjkirim.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'sjkirim.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->leftJoin(DB::raw(sprintf( '(%s) AS T1', $sum->toSql() )), function($join){
					$join->on('T1.SJKir', '=', 'sjkirim.SJKir');
				})
			->orderBy('sjkirim.id', 'asc')
			->get();
		}else{
			$sjkirim = SJKirim::select([
				'qttd',
				'sjkirim.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'sjkirim.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->leftJoin(DB::raw(sprintf( '(%s) AS T1', $sum->toSql() )), function($join){
					$join->on('T1.SJKir', '=', 'sjkirim.SJKir');
				})
			->where('PPN', $this->PPNNONPPN)
			->orderBy('sjkirim.id', 'asc')
			->get();
		}

		if(in_array("index", $this->access)){
			return view('pages.sjkirim.indexs')
			->with('url', 'sjkirim')
			->with('sjkirims', $sjkirim)
			->with('top_menu_sel', 'menu_sjkirim')
			->with('page_title', 'Surat Jalan Kirim')
			->with('page_description', 'Index');
		}else
			return redirect()->back();
	}

	public function create()
	{
		$reference = Reference::find(Input::get('id'));
		
		$maxperiode = Transaksi::leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->where('Reference', $reference->Reference)
		->max('Periode');
		$po = Transaksi::leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->where('transaksi.Reference', $reference->Reference)
		->where('po.Periode', $maxperiode)
		->first();
		$min = $po->Tgl;
		
		$sjkirim = SJKirim::select([
			DB::raw('MAX(sjkirim.id) AS maxid')
		])
		->first();
		
		if(in_array("create", $this->access)){
			return view('pages.sjkirim.create')
			->with('url', 'sjkirim')
			->with('reference', $reference)
			->with('min', $min)
			->with('sjkirim', $sjkirim)
			->with('top_menu_sel', 'menu_sjkirim')
			->with('page_title', 'Surat Jalan Kirim')
			->with('page_description', 'Create');
		}else
			return redirect()->back();
	}
	
	public function getCreate2(Request $request, $id)
	{ 
		Session::put('SJKir', $request->SJKir);
		Session::put('Tgl', $request->Tgl);
		Session::put('JS', $request->JS);
		Session::put('Reference', $request->Reference);

		$JS = Session::get('JS');
		$Reference = Session::get('Reference');
		
		$referenceid = Reference::where('pocustomer.id', $id)
		->first();
		
		$transaksis = Transaksi::select([
			'transaksi.*',
			'po.Tgl as tglpo',
			'po.POCode',
			'project.Project',
		])
		->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
		->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
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
		
		if(in_array("create2", $this->access)){
			return view('pages.sjkirim.create2')
			->with('url', 'sjkirim')
			->with('referenceid', $referenceid)
			->with('transaksis', $transaksis)
			->with('isisjkirim', $isisjkirim)
			->with('sjkirim', $sjkirim)
			->with('top_menu_sel', 'menu_sjkirim')
			->with('page_title', 'Surat Jalan Kirim')
			->with('page_description', 'Choose');
		}else
			return redirect()->back();
	}
	
	public function getCreate3(Request $request, $id)
	{
		$input = Input::only('checkbox');
		$purchases = $input['checkbox'];
		foreach ($purchases as $key => $purchases)
		{
			$Purchase[] = $input['checkbox'][$key];
		}

		$Tgl = Session::get('Tgl');
		$Reference = Session::get('Reference');
		
		$referenceid = Reference::where('pocustomer.id', $id)
		->first();
		
		$transaksis = Transaksi::select([
			'transaksi.*',
			'inventory.Type',
			'inventory.Kumbang',
			'project.Project',
		])
		->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
		->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
		->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
		->where('transaksi.Reference', $Reference)
		->whereIn('transaksi.Purchase', $Purchase)
		->orderBy('transaksi.id', 'asc')
		->get();
		
		$last_sjkirim = SJKirim::max('id');
		
		$last_isisjkirim = IsiSJKirim::max('id');
		
		$last_periode = Periode::max('id');
		
		$maxperiode = Periode::where('Reference', $Reference)->max('Periode');
		
		$maxisisjkir = IsiSJKirim::max('IsiSJKir');

		$ECont = Periode::whereIn('Purchase', $Purchase)
		//->whereRaw('(SELECT MAX(Periode) FROM periode WHERE Reference = ?)', $Reference)
		->where('Periode', $maxperiode)
		->first();
		
		$end = str_replace('/', '-', $Tgl);
		$end2 = strtotime("-1 day +1 month", strtotime($end));
		$end3 = date("d/m/Y", $end2);
		
		if(isset($ECont)){
			$tglE = $ECont->E;
			$periode = $ECont->Periode;
		}else{
			$tglE = $end3;
			$periode = 1;
		}
		
		if(in_array("create3", $this->access)){
			return view('pages.sjkirim.create3')
			->with('url', 'sjkirim')
			->with('tglE', $tglE)
			->with('periode', $periode)
			->with('referenceid', $referenceid)
			->with('transaksis', $transaksis)
			->with('last_sjkirim', $last_sjkirim)
			->with('last_isisjkirim', $last_isisjkirim)
			->with('last_periode', $last_periode)
			->with('maxperiode', $maxperiode)
			->with('maxisisjkir', $maxisisjkir)
			->with('top_menu_sel', 'menu_sjkirim')
			->with('page_title', 'Surat Jalan Kirim')
			->with('page_description', 'Item');
		}else
			return redirect()->back();
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
			'Keterangan' => $request['Keterangan'],
			'FormMuat' => $request['FormMuat'],
		]);
		
		$input = Input::all();
		$isisjkirims = $input['isisjkirimid'];
		foreach ($isisjkirims as $key => $isisjkirim)
		{
			$isisjkirim = new IsiSJKirim;
			$isisjkirim->id = $input['isisjkirimid'][$key];
			$isisjkirim->IsiSJKir = $input['IsiSJKir'][$key];
			$isisjkirim->QKirim = $input['QKirim'][$key];
			$isisjkirim->Warehouse = $input['Warehouse'][$key];
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
		
		$data = Invoice::where('Reference', $Reference)
		->where('Periode', $input['Periode'])
		->where('JSC', $JS)->first();
		$data->update(['Times' => $data->Times + 1]);
		
		$data = InvoicePisah::where('Reference', $Reference)
		->where('POCode', $input['POCode'][0])
		->where('Periode', $input['Periode'])
		->where('JSC', $JS)->first();
		$data->update(['Times' => $data->Times + 1]);
		
		$inventories = $input['ICode'];
		foreach ($inventories as $key => $inventory)
		{
			if($input['Warehouse'][$key] == 'Kumbang'){
				$warehouse = 'Kumbang';
			}elseif($input['Warehouse'][$key] == 'BulakSereh'){
				$warehouse = 'BulakSereh';
			}elseif($input['Warehouse'][$key] == 'Legok'){
				$warehouse = 'Legok';
			}elseif($input['Warehouse'][$key] == 'CitraGarden'){
				$warehouse = 'CitraGarden';
			}
			$data = Inventory::where('Code', $input['ICode'][$key])
			->first();
			$data->update([$warehouse => $data->$warehouse - $input['QKirim'][$key]]);
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create SJKirim on SJKir '.$SJKir;
		$history->save();
		
		Session::forget('SJKir');
		Session::forget('Tgl');
		Session::forget('JS');
		Session::forget('Reference');
		
		return redirect()->route('sjkirim.show', $request['sjkirimid']);
	}

	public function show($id)
	{
		$sjkirim = SJKirim::select([
			'sjkirim.*',
			'sjkirim.id as sjkirid', 
			'pocustomer.id as pocusid', 
		])
		->leftJoin('pocustomer', 'sjkirim.Reference', '=', 'pocustomer.Reference')
		->where('sjkirim.id', $id)
		->first();

		$isisjkirims = IsiSJKirim::select([
			'isisjkirim.*',
			'sjkirim.*',
			'periode.Periode',
			'transaksi.*',
			'project.*',
			'customer.*'
		])
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
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

		$periode = Periode::leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
		->where('isisjkirim.SJKir', $sjkirim->SJKir)
		->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Jual")')
		->first();
		
		$maxperiode = Periode::where('Reference', $sjkirim->Reference)
		->max('Periode');
		if($periode->Periode == $maxperiode){
			$qttdcheck = 0;
		}else{
			$qttdcheck = 1;
		}
		
		if(in_array("show", $this->access)){
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
		}else
			return redirect()->back();
	}

	public function edit($id)
	{
		$sjkirim = SJKirim::find($id);
		
		$maxperiode = Transaksi::leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->where('Reference', $sjkirim->Reference)
		->max('Periode');
		$po = Transaksi::leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->where('transaksi.Reference', $sjkirim->Reference)
		->where('po.Periode', $maxperiode)
		->first();
		$min = $po->Tgl;
		
		$isisjkirims = IsiSJKirim::select([
			'isisjkirim.id as isisjkirimid',
			'isisjkirim.*',
			'sjkirim.*',
			'transaksi.id as transaksiid',
			'transaksi.*',
			'inventory.*',
			'project.Project',
		])
		->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
		->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
		->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
		->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
		->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
		->where('isisjkirim.SJKir', $sjkirim->SJKir)
		->orderBy('isisjkirim.id', 'asc')
		->get();

		if(in_array("edit", $this->access)){
			return view('pages.sjkirim.edit')
			->with('url', 'sjkirim')
			->with('sjkirim', $sjkirim)
			->with('isisjkirims', $isisjkirims)
			->with('min', $min)
			->with('top_menu_sel', 'menu_sjkirim')
			->with('page_title', 'Surat Jalan Kirim')
			->with('page_description', 'Edit');
		}else
			return redirect()->back();
	}

	public function update(Request $request, $id)
	{
		$sjkirim = SJKirim::find($id);
		$sjkirim->Tgl = $request['Tgl'];
		$sjkirim->NoPolisi = $request['NoPolisi'];
		$sjkirim->Sopir = $request['Sopir'];
		$sjkirim->Kenek = $request['Kenek'];
		$sjkirim->FormMuat = $request['FormMuat'];
		$sjkirim->Keterangan = $request['Keterangan'];
		$sjkirim->save();
		
		$isisjkirim = IsiSJKirim::where('isisjkirim.SJKir', $sjkirim -> SJKir)->orderBy('id', 'asc');
		$purchase = $isisjkirim->pluck('Purchase');
		$warehouses = $isisjkirim->pluck('isisjkirim.Warehouse');
		$qkirim = $isisjkirim->pluck('QKirim');
		$input = Input::all();
		$inventories = $input['ICode'];
		foreach ($inventories as $key => $inventory)
		{
			if($input['Warehouse'][$key] == 'Kumbang'){
				$warehouse = 'Kumbang';
			}elseif($input['Warehouse'][$key] == 'BulakSereh'){
				$warehouse = 'BulakSereh';
			}elseif($input['Warehouse'][$key] == 'Legok'){
				$warehouse = 'Legok';
			}elseif($input['Warehouse'][$key] == 'CitraGarden'){
				$warehouse = 'CitraGarden';
			}
			if($warehouses[$key] == 'Kumbang'){
				$warehouse2 = 'Kumbang';
			}elseif($warehouses[$key] == 'BulakSereh'){
				$warehouse2 = 'BulakSereh';
			}elseif($warehouses[$key] == 'Legok'){
				$warehouse2 = 'Legok';
			}elseif($warehouses[$key] == 'CitraGarden'){
				$warehouse2 = 'CitraGarden';
			}
			if($warehouse==$warehouse2){
				$data = Inventory::where('Code', $input['ICode'][$key])
				->first();
				$data->update([$warehouse => $data->$warehouse + $qkirim[$key] - $request['QKirim'][$key]]);
			}else{
				$data = Inventory::where('Code', $input['ICode'][$key])
				->first();
				$data->update([$warehouse => $data->$warehouse - $request['QKirim'][$key], $warehouse2 => $data->$warehouse2 + $qkirim[$key]]);
			}
		}

		$transaksis = $input['transaksiid'];
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
			$periode->S = $input['Tgl'];
			$periode->save();
		}
		
		$isisjkirims = $input['isisjkirimid'];
		foreach ($isisjkirims as $key => $isisjkirim)
		{
			$isisjkirim = IsiSJKirim::find($isisjkirims[$key]);
			$isisjkirim->QKirim = $input['QKirim'][$key];
			$isisjkirim->Warehouse = $input['Warehouse'][$key];
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
			'transaksi.id as transaksiid',
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
		
		if(in_array("qtertanda", $this->access)){
			return view('pages.sjkirim.qtertanda')
			->with('url', 'sjkirim')
			->with('sjkirim', $sjkirim)
			->with('isisjkirims', $isisjkirims)
			->with('Tgl', $Tgl)
			->with('periode', $periode)
			->with('top_menu_sel', 'menu_sjkirim')
			->with('page_title', 'Surat Jalan Kirim')
			->with('page_description', 'QTertanda');
		}else
			return redirect()->back();
	}
	
	public function postQTertanda(Request $request, $id)
	{
		$sjkirim = SJKirim::find($id);
		
		$input = Input::all();
		$transaksis = $request->transaksiid;
		foreach ($transaksis as $key => $transaksi)
		{
			$transaksi = Transaksi::find($transaksis[$key]);
			$transaksi->QSisaKir = $transaksi->QSisaKir+$input['QTertanda2'][$key]-$input['QTertanda'][$key];
			$transaksi->QSisaKem = $transaksi->QSisaKem-$input['QTertanda2'][$key]+$input['QTertanda'][$key];
			$transaksi->save();
		}
		Transaksi::where('JS', 'Jual')->update(['QSisaKem' => '0']);
		
		$isisjkirims = $request->id;
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

	public function getSJ($id)
	{
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$sjkirim = SJKirim::find($id);
		
		$isisjkirims = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
		->where('isisjkirim.SJKir', $sjkirim->SJKir)
		->orderBy('isisjkirim.id', 'asc')
		->get();
		
		$sjkirim = IsiSJKirim::leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
		->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
		->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
		->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
		->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
		->where('sjkirim.SJKir', $sjkirim->SJKir)
		->first();
		
		$document = $phpWord->loadTemplate(public_path('/template/SJ.docx'));
		
		$document->setValue('Company', ''.$sjkirim->Company.'');
		$document->setValue('PCode', ''.$sjkirim->PCode.'');
		$document->setValue('Project', ''.$sjkirim->Project.'');
		$document->setValue('ProjAlamat', ''.$sjkirim->ProjAlamat.'');
		$document->setValue('Customer', ''.$sjkirim->Customer.'');
		$document->setValue('CustPhone', ''.$sjkirim->CustPhone.'');
		$document->setValue('SJKir', $sjkirim->SJKir);
		$document->setValue('Tanggal', ''.date("d/m/Y").'');
		$document->setValue('JS', $sjkirim->JS);
		$document->setValue('NoPolisi', $sjkirim->NoPolisi);
		$document->setValue('Keterangan', $sjkirim->Keterangan);
		$document->setValue('FormMuat', $sjkirim->FormMuat);
		$document->setValue('Sopir', $sjkirim->Sopir);
		$document->setValue('Kenek', $sjkirim->Kenek);

		foreach ($isisjkirims as $key => $isisjkirim)
		{
			$key2 = $key+1;
			$document->setValue('Key'.$key, ''.$key2.'');
			$document->setValue('Barang'.$key, ''.$isisjkirim->Barang.'');
			$document->setValue('Quantity'.$key, ''.$isisjkirim->QKirim.' PCS');
		}
		
		for($x=0;$x<20;$x++){
			$document->setValue('Key'.$x, '');
			$document->setValue('Barang'.$x, '');
			$document->setValue('Quantity'.$x, '');
		}
		
		$user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
		$path = sprintf("C:\Users\Public\Documents\SJ_");
		$clear = str_replace("/","_",$sjkirim->SJKir);
		$download = sprintf('%s.docx', $clear);
		
		$document->saveAs($path.$download);
		
		Session::flash('message', 'Downloaded to Server Public Documents file name SJ_'.$download);
		return redirect()->route('sjkirim.show', $id);
	}
	
	public function destroy(Request $request, $id)
	{
		$sjkirim = SJKirim::find($id);
		
		$isisjkirim = IsiSJKirim::leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
		->where('isisjkirim.SJKir', $sjkirim->SJKir);
		
		$transaksiid = $isisjkirim->pluck('transaksi.id');
		$warehouses = $isisjkirim->pluck('isisjkirim.Warehouse');
		$qkirim = $isisjkirim->pluck('isisjkirim.QKirim');
		$qtertanda = $isisjkirim->pluck('isisjkirim.QTertanda');
		$qsisakeminsert = $isisjkirim->pluck('isisjkirim.QSisaKemInsert');
		$icode = $isisjkirim->pluck('ICode');
		$pocode = $isisjkirim->pluck('POCode');
		$JS = $isisjkirim->first()->JS;
		
		$inventories = $icode;
		foreach ($inventories as $key => $inventory)
		{
			if($warehouses[$key] == 'Kumbang'){
				$warehouse = 'Kumbang';
			}elseif($warehouses[$key] == 'BulakSereh'){
				$warehouse = 'BulakSereh';
			}elseif($warehouses[$key] == 'Legok'){
				$warehouse = 'Legok';
			}elseif($warehouses[$key] == 'CitraGarden'){
				$warehouse = 'CitraGarden';
			}
			$data = Inventory::where('Code', $icode[$key])
			->first();
			$data->update([$warehouse => $data->$warehouse + $qkirim[$key]]);
		}
		
		$data = Invoice::where('Reference', $sjkirim->Reference)
		->where('Periode', $request->Periode)
		->where('JSC', $JS)->first();
		$data->update(['Times' => $data->Times - 1]);
		
		$data = InvoicePisah::where('Reference', $sjkirim->Reference)
		->where('Periode', $request->Periode)
		->whereIn('POCode', $pocode)
		->where('JSC', $JS)->first();
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
		DB::statement('ALTER TABLE periode auto_increment = 1;');
		
		IsiSJKirim::where('SJKir', $sjkirim->SJKir)->delete();
		DB::statement('ALTER TABLE isisjkirim auto_increment = 1;');
		
		SJKirim::destroy($id);
		DB::statement('ALTER TABLE sjkirim auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete SJKirim on SJKir '.$sjkirim->SJKir;
		$history->save();
		
		Session::flash('message', 'Delete is successful!');

		return redirect()->route('sjkirim.index');
	}
}