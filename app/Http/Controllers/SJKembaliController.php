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
use App\TransaksiHilang;
use App\History;
use App\Inventory;
use App\Invoice;
use App\InvoicePisah;
use Session;
use DB;
use Auth;

class SJKembaliController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Admin'||Auth::user()->access=='CUSTINVPPN'||Auth::user()->access=='CUSTINVNONPPN'))
				$this->access = array("index", "create", "create2", "create3", "show", "edit", "qterima");
			else if(Auth::check()&&(Auth::user()->access=='POINVPPN'||Auth::user()->access=='POINVNONPPN'))
				$this->access = array("index", "create", "create2", "create3", "show", "edit", "qterima");
			else
				$this->access = array("");
			
			if(Auth::user()->access=='POINVPPN'||Auth::user()->access=='CUSTINVPPN')
				$this->PPNNONPPN = 1;
			else if(Auth::user()->access=='POINVNONPPN'||Auth::user()->access=='CUSTINVNONPPN')
				$this->PPNNONPPN = 0;
    return $next($request);
    });
	}
	
	public function index()
	{
		$sum = IsiSJKembali::select([
				'isisjkembali.SJKem',
				DB::raw('sum(isisjkembali.QTertanda) AS qtrima')
			])
			->groupBy('isisjkembali.SJKem');
			
		if(Auth::user()->access == 'Admin'){
			$sjkembali = SJKembali::select([
				'qtrima',
				'sjkembali.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'sjkembali.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->leftJoin(DB::raw(sprintf( '(%s) AS T1', $sum->toSql() )), function($join){
					$join->on('T1.SJKem', '=', 'sjkembali.SJKem');
				})
			->orderBy('sjkembali.id', 'asc')
			->get();
		}else{
			$sjkembali = SJKembali::select([
				'qtrima',
				'sjkembali.*',
				'project.Project',
				'customer.Company',
			])
			->leftJoin('pocustomer', 'sjkembali.Reference', '=', 'pocustomer.Reference')
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->leftJoin(DB::raw(sprintf( '(%s) AS T1', $sum->toSql() )), function($join){
					$join->on('T1.SJKem', '=', 'sjkembali.SJKem');
				})
			->where('PPN', $this->PPNNONPPN)
			->orderBy('sjkembali.id', 'asc')
			->get();
		}

		if(in_array("index", $this->access)){
			return view('pages.sjkembali.indexs')
			->with('url', 'sjkembali')
			->with('sjkembalis', $sjkembali)
			->with('top_menu_sel', 'menu_sjkembali')
			->with('page_title', 'Surat Jalan Kembali')
			->with('page_description', 'Index');
		}else
			return redirect()->back();
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
		
		if(in_array("create", $this->access)){
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
		}else
			return redirect()->back();
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
		->groupBy('isisjkirim.SJKir', 'transaksi.Purchase')
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
		
		if(in_array("create2", $this->access)){
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
		}else
			return redirect()->back();
	}
	
	public function getCreate3(Request $request, $id)
	{
		$input = Input::only('checkbox');
		$purchases = $input['checkbox'];
		foreach ($purchases as $key => $purchase)
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
		
		if(in_array("create3", $this->access)){
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
		}else
			return redirect()->back();
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
			'NoPolisi' => $request['NoPolisi'],
			'Sopir' => $request['Sopir'],
			'Kenek' => $request['Kenek'],
		]);
		
		$maxid = Periode::select([
			DB::raw('MAX(periode.id) AS maxid')
		])
		->where('periode.Reference', $Reference)
		->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
		->groupBy('periode.IsiSJKir')
		->orderBy('periode.id', 'desc')
		->pluck('periode.maxid');
		
		$periodes = Periode::whereIn('periode.id', $maxid)
		->where('periode.Reference', $Reference)
		->orderBy('periode.id', 'asc')
		->get();
		$start = $periodes->pluck('S');
		$quantity = $periodes->pluck('Quantity');
		$isisjkir = $periodes->pluck('IsiSJKir');
		$purchase = $periodes->pluck('Purchase');
		
		$periodeid = Periode::select([
			DB::raw('MAX(periode.id) AS maxid')
		])
		->first();
		
		$input = Input::all();
		foreach ($periodes as $key => $periode)
		{
			$periode = new Periode;
			$periode->id = $periodeid->maxid+$key+1;
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
		
		$data = Invoice::where('Reference', $Reference)
		->where('Periode', $input['Periode'])
		->where('JSC', 'Sewa')->first();
		$data->update(['TimesKembali' => $data->TimesKembali + 1]);
		
		$data = InvoicePisah::where('Reference', $Reference)
		->where('Periode', $input['Periode'])
		->where('JSC', 'Sewa')->first();
		$data->update(['TimesKembali' => $data->TimesKembali + 1]);
		
		$storeprocs = $input['id'];
		foreach ($storeprocs as $key => $storeproc)
		{
			DB::select('CALL insert_sjkembali(?,?,?,?)',array($input['QTertanda'][$key], $input['Purchase'][$key], $input['Periode'], $SJKem));
		}
		
		DB::select('CALL insert_sjkembali2');
		/*$delpurchase = IsiSJKembali::whereNull('Warehouse')
		->pluck('Purchase');
		
		IsiSJKembali::whereIn('Purchase', $delpurchase)
		->where('SJKem', $SJKem)
		->delete();
		
		Periode::whereIn('Purchase', $delpurchase)
		->where('SJKem', $SJKem)
		->delete();*/
		
		$isisjkembaliid = IsiSJKembali::select([
			DB::raw('MAX(isisjkembali.id) AS maxid')
		])
		->first();
		
		$periode2s = Periode::where('periode.SJKem', $SJKem)
		->orderBy('periode.id', 'asc')
		->get();
		$qtertanda = $periode2s->pluck('Quantity');
		$isisjkir2 = $periode2s->pluck('IsiSJKir');
		$purchase2 = $periode2s->pluck('Purchase');
		
		$isisjkembalis = $purchase2;
		foreach ($isisjkembalis as $key => $isisjkembali)
		{
			$isisjkembali = new IsiSJKembali;
			$isisjkembali->id = $isisjkembaliid->maxid+$key+1;
			$isisjkembali->IsiSJKem = $isisjkembaliid->maxid+$key+1;
			$isisjkembali->QTertanda = $qtertanda[$key];
			$isisjkembali->Purchase = $purchase2[$key];
			$isisjkembali->SJKem = $SJKem;
			$isisjkembali->Periode = $input['Periode'];
			$isisjkembali->IsiSJKir = $isisjkir2[$key];
			$isisjkembali->save();
		}
		
		$isisjkembali2s = $input['Purchase'];
		foreach ($isisjkembali2s as $key => $isisjkembali)
		{
			IsiSJKembali::where('Purchase', $input['Purchase'][$key])
			->update(['Warehouse' => $input['Warehouse'][$key]]);
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create SJKembali on SJKem '.$SJKem;
		$history->save();

		//Session::forget('SJKem');
		//Session::forget('Tgl');
		//Session::forget('Reference');
		
		return redirect()->route('sjkembali.show', $request['sjkembaliid']);
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
		
		$periodecheck = IsiSJKembali::select('isisjkembali.Periode')
		->where('isisjkembali.SJKem', $sjkembali->SJKem)
		->first();
		$periodemax = Periode::select([
			DB::raw('max(periode.Periode) as maxper')
		])
		->where('periode.Reference', $sjkembali->Reference)
		->first();
		if($periodecheck->Periode >= $periodemax->maxper){
			$periodecheck = 0;
		}else{
			$periodecheck = 1;
		}
		
		$transaksihilang = TransaksiHilang::select('transaksihilang.*', 'transaksi.Barang')
		->leftJoin('transaksi', 'transaksihilang.Purchase', '=', 'transaksi.Purchase')
		->where('SJ', $sjkembali->SJKem)
		->where('SJType', 'Kembali')
		->get();
		
		if(in_array("show", $this->access)){
			return view('pages.sjkembali.show')
			->with('url', 'sjkembali')
			->with('sjkembali', $sjkembali)
			->with('isisjkembali', $isisjkembali)
			->with('isisjkembalis', $isisjkembalis)
			->with('qtrimacheck', $qtrimacheck)
			->with('periodecheck', $periodecheck)
			->with('transaksihilang', $transaksihilang)
			->with('top_menu_sel', 'menu_sjkembali')
			->with('page_title', 'Surat Jalan Kembali')
			->with('page_description', 'View');
		}else
			return redirect()->back();
	}

	public function edit($id)
	{
		$sjkembali = SJKembali::find($id);
		
		$maxperiode = Periode::where('Reference', $sjkembali->Reference)->max('Periode');
		
		$TglMin = Periode::select([
			'periode.S',
		])
		->where('periode.Reference', $sjkembali->Reference)
		->where('periode.Periode', $maxperiode)
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

		if(in_array("edit", $this->access)){
			return view('pages.sjkembali.edit')
			->with('url', 'sjkembali')
			->with('sjkembali', $sjkembali)
			->with('TglMin', $TglMin)
			->with('TglMax', $TglMax)
			->with('isisjkembalis', $isisjkembalis)
			->with('top_menu_sel', 'menu_sjkembali')
			->with('page_title', 'Surat Jalan Kembali')
			->with('page_description', 'Edit');
		}else
			return redirect()->back();
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
			$data->update(['Quantity' => $qtertanda2[$key], 'E' => $request->Tgl2]);
		}
		
		$sjkembali = SJKembali::find($id);
		$sjkembali->Tgl = $request['Tgl2'];
		$sjkembali->NoPolisi = $request['NoPolisi'];
		$sjkembali->Sopir = $request['Sopir'];
		$sjkembali->Kenek = $request['Kenek'];
		$sjkembali->save();

		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Update SJKembali on SJKem '.$sjkembali->SJKem;
		$history->save();
		
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
		->where('periode.SJKem', $sjkembali->SJKem)
		->first();
		
		if(in_array("qterima", $this->access)){
			return view('pages.sjkembali.qterima')
			->with('url', 'sjkembali')
			->with('sjkembali', $sjkembali)
			->with('isisjkembalis', $isisjkembalis)
			->with('QTerima2', $QTerima2)
			->with('Tgl', $Tgl)
			->with('top_menu_sel', 'menu_sjkirim')
			->with('page_title', 'Surat Jalan Kembali')
			->with('page_description', 'QTerima');
		}else
			return redirect()->back();
	}
	
	public function postQTerima(Request $request, $id)
	{
		$sjkembali = SJKembali::find($id);
		
		$isisjkembali = IsiSJKembali::where('isisjkembali.SJKem', $sjkembali->SJKem);
		$isisjkir = $isisjkembali->pluck('IsiSJKir');
		$qterima = $isisjkembali->pluck('QTerima');
		
		$input = Input::all();
		
		$inventories = $input['Barang'];
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
			$data = Inventory::where('Barang', $input['Barang'][$key])
			->where('Type', 'SECOND')
			->first();
			$data->update([$warehouse => $data->$warehouse - $input['QTerima2'][$key] + $input['QTerima'][$key]]);
		}
		
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
			$data = IsiSJKembali::where('IsiSJKir', $isisjkir[$key])->where('SJKem', $sjkembali->SJKem)->first();
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
	
	public function getSPB($id)
	{
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$reference = Reference::find($id);
		
		$maxperiode = Periode::select([
			DB::raw('max(periode.Periode) as maxperiode'),
		])
		->where('periode.Reference', $reference->Reference)
		->first();
		
		$transaksis = Transaksi::where('transaksi.Reference', $reference->Reference)
		->where('transaksi.JS', 'Sewa')
		->orderBy('transaksi.id', 'asc')
		->get();
		
		$transaksi = Reference::leftJoin('invoice', 'pocustomer.Reference', '=', 'invoice.Reference')
		->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
		->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
		->where('pocustomer.id', $id)
		->where('invoice.Periode', $maxperiode->maxperiode)
		->first();
		
		$document = $phpWord->loadTemplate(public_path('/template/SPB.docx'));
		
		$document->setValue('Company', ''.$transaksi->Company.'');
		$document->setValue('Project', ''.$transaksi->Project.'');
		$document->setValue('ProjAlamat', ''.$transaksi->ProjAlamat.'');
		$document->setValue('Customer', ''.$transaksi->Customer.'');
		$document->setValue('CustPhone', ''.$transaksi->CustPhone.'');
		$document->setValue('Invoice', ''.$transaksi->Invoice.'');
		$document->setValue('Tanggal', ''.date("d/m/Y").'');
		$document->setValue('JS', 'Sewa');

		foreach ($transaksis as $key => $transaksis)
		{
			$key2 = $key+1;
			$document->setValue('Key'.$key, ''.$key2.'');
			$document->setValue('Barang'.$key, ''.$transaksis->Barang.'');
			$document->setValue('Quantity'.$key, ''.$transaksis->QSisaKem.' PCS');
		}
		
		for($x=0;$x<20;$x++){
			$document->setValue('Key'.$x, '');
			$document->setValue('Barang'.$x, '');
			$document->setValue('Quantity'.$x, '');
		}
		
		$user = substr(gethostbyaddr($_SERVER['REMOTE_ADDR']), 0, -3);
		$path = sprintf("C:\Users\Public\Documents\SPB\SPB_", $user);
		$clear = str_replace("/","_",$transaksi->Invoice);
		$download = sprintf('%s.docx', $clear);
		
		$document->saveAs($path.$download);
		
		Session::flash('message', 'Downloaded to Server Public Documents file name SPB_'.$download);
		return redirect()->route('reference.show', $id);
	}

	public function destroy(Request $request, $id)
	{
		$sjkembali = SJKembali::find($id);
		
		$periode = Periode::where('periode.SJKem', $sjkembali->SJKem);
		$quantity = $periode->pluck('Quantity');
		$isisjkembali = IsiSJKembali::where('isisjkembali.SJKem', $sjkembali->SJKem);
		$warehouse = $isisjkembali->pluck('Warehouse');
		$purchase = $isisjkembali->pluck('Purchase');
		$isisjkir = $isisjkembali->pluck('IsiSJKir');
		$qterima = $isisjkembali->pluck('QTerima');
		$qtertanda = $isisjkembali->pluck('QTertanda');
		$maxperiode = $isisjkembali->select([DB::raw('max(isisjkembali.Periode) as maxperiode')])->first();
		$barang = Transaksi::whereIn('transaksi.Purchase', $purchase)
		->pluck('transaksi.Barang');
		
		$inventories = $barang;
		foreach ($inventories as $key => $inventory)
		{
			if($warehouse[$key] == 'Kumbang'){
				$warehouse = 'Kumbang';
			}elseif($warehouse[$key] == 'BulakSereh'){
				$warehouse = 'BulakSereh';
			}elseif($warehouse[$key] == 'Legok'){
				$warehouse = 'Legok';
			}elseif($warehouse[$key] == 'CitraGarden'){
				$warehouse = 'CitraGarden';
			}
			$data = Inventory::where('Barang', $barang[$key])
			->where('Type', 'SECOND')
			->first();
			$data->update([$warehouse => $data->$warehouse - $qterima[$key]]);
		}
		
		$data = Invoice::where('Reference', $sjkembali->Reference)
		->where('Periode', $maxperiode->maxperiode)
		->where('JSC', 'Sewa')->first();
		$data->update(['TimesKembali' => $data->TimesKembali - 1]);
		
		$data = InvoicePisah::where('Reference', $sjkembali->Reference)
		->where('Periode', $maxperiode->maxperiode)
		->where('JSC', 'Sewa')->first();
		$data->update(['TimesKembali' => $data->TimesKembali - 1]);
		
		$transaksis = $purchase;
		foreach ($transaksis as $key => $transaksi)
		{
			$data = Transaksi::where('Purchase', $purchase[$key])->first();
			$data->update(['QSisaKem' => $data->QSisaKem + $qterima[$key]]);
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
		DB::statement('ALTER TABLE periode auto_increment = 1;');
		
		IsiSJKembali::where('SJKem', $sjkembali->SJKem)->delete();
		DB::statement('ALTER TABLE isisjkembali auto_increment = 1;');
		
		SJKembali::destroy($id);
		DB::statement('ALTER TABLE sjkembali auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete SJKembali on SJKem '.$sjkembali->SJKem;
		$history->save();
		
		Session::flash('message', 'Delete is successful!');

		return redirect()->route('sjkembali.index');
	}
}
