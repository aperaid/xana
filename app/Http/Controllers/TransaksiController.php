<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;
use App\Periode;
use App\Invoice;
use App\InvoicePisah;
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
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access == 'Admin'||Auth::user()->access()=='CUSTINVPPN'||Auth::user()->access()=='CUSTINVNONPPN'))
				$this->access = array("index", "claimcreate", "claimcreate2", "claimcreate3");
			else if(Auth::check()&&(Auth::user()->access()=='POINVPPN'||Auth::user()->access()=='POINVNONPPN'))
				$this->access = array("index", "claimcreate", "claimcreate2", "claimcreate3");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
	public function index()
	{
		$maxid = Periode::select([
			'periode.Reference',
			'periode.IsiSJKir',
			'periode.Periode',
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
			'per.maxperiode',
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
		->leftJoin(DB::raw('(select Reference, IsiSJKir, MAX(Periode) AS maxperiode from Periode group by Reference) AS per'), function($join){
			$join->on('per.Reference', '=', 'periode.Reference');
			$join->on('per.IsiSJKir', '=', 'periode.IsiSJKir');
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
		
		if(in_array("index", $this->access)){
			return view('pages.transaksi.indexs')
			->with('url', 'transaksi')
			->with('maxid', $maxid)
			->with('transaksiss', $transaksis)
			->with('transaksijs', $transaksij)
			->with('transaksics', $transaksic)
			->with('top_menu_sel', 'menu_transaksi')
			->with('page_title', 'Transaksi')
			->with('page_description', 'Index');
		}else
			return redirect()->back();
	}
	
	public function Extend(Request $request)
	{
		$invoice = Invoice::find($request->id);
		$last_invoice = Invoice::max('id')+1;
		
		$periodes = Periode::leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
		->where('periode.Reference', $invoice->Reference)
		->where('periode.Periode', $invoice->Periode)
		->whereNull('periode.SJKem')
		->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")');
		$quantity = $periodes->pluck('periode.Quantity');
		$isisjkir = $periodes->pluck('periode.IsiSJKir');
		$purchase = $periodes->pluck('periode.Purchase');
		
		$termin = Invoice::where('reference', $invoice->Reference)->where('periode', 1)->first();
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
				'id' => $last_invoice,
				'Invoice' => $projectcode->PCode."/1/".substr($invoice->Tgl, 3, -5).substr($invoice->Tgl, 6)."/BDN",
				'JSC' => 'Sewa',
				'Tgl' => $TglInvoice2,
				'Reference' => $invoice->Reference,
				'Periode' => $Periode,
				'PPN' => $invoice->PPN,
				'Count' => 1,
				'Termin' => $termin->Termin,
			]);
		}else{
			Invoice::Create([
				'id' => $last_invoice,
				'Invoice' =>  $projectcode->PCode."/".$Count."/".substr($invoice->Tgl, 3, -5).substr($invoice->Tgl, 6)."/BDN",
				'JSC' => 'Sewa',
				'Tgl' => $TglInvoice2,
				'Reference' => $invoice->Reference,
				'Periode' => $Periode,
				'PPN' => $invoice->PPN,
				'Count' => $Count,
				'Termin' => $termin->Termin,
			]);
		}
		
		$invoicepisahs = InvoicePisah::where('Reference', $invoice->Reference)
		->where('Periode', $invoice->Periode)
		->where('JSC', 'Sewa')
		->get();
		
		$last_invoicepisah = InvoicePisah::max('id')+1;
		
		foreach ($invoicepisahs as $key => $invoicepisah){
		$x = $invoicepisah->Abjad;
		if($x==1)$y='';else if($x==2)$y='A';else if($x==3)$y='B';else if($x==4)$y='C';else if($x==5)$y='D';else if($x==6)$y='E';else if($x==7)$y='F';else if($x==8)$y='G';else if($x==9)$y='H';else if($x==10)$y='I';else if($x==11)$y='J';else if($x==12)$y='K';else if($x==13)$y='L';else if($x==14)$y='M';else if($x==15)$y='N';else if($x==16)$y='O';else if($x==17)$y='P';else if($x==18)$y='Q';else if($x==19)$y='R';else if($x==20)$y='S';else if($x==21)$y='T';else if($x==22)$y='U';else if($x==23)$y='V';else if($x==24)$y='W';else if($x==25)$y='X';else if($x==26)$y='Y';else if($x==27)$y='Z';
		if(substr($invoice->Tgl,6)!=date('Y')){
			InvoicePisah::Create([
				'id' => $last_invoicepisah+$key,
				'Invoice' => $projectcode->PCode.$y."/1/".substr($invoicepisah->Tgl, 3, -5).substr($invoicepisah->Tgl, 6)."/BDN",
				'JSC' => 'Sewa',
				'Tgl' => $TglInvoice2,
				'Reference' => $invoicepisah->Reference,
				'Periode' => $Periode,
				'PPN' => $invoicepisah->PPN,
				'Count' => 1,
				'Termin' => $termin->Termin,
				'POCode' => $invoicepisah->POCode,
				'Abjad' => $invoicepisah->Abjad,
			]);
		}else{
			InvoicePisah::Create([
				'id' => $last_invoicepisah+$key,
				'Invoice' =>  $projectcode->PCode.$y."/".$Count."/".substr($invoicepisah->Tgl, 3, -5).substr($invoicepisah->Tgl, 6)."/BDN",
				'JSC' => 'Sewa',
				'Tgl' => $TglInvoice2,
				'Reference' => $invoicepisah->Reference,
				'Periode' => $Periode,
				'PPN' => $invoicepisah->PPN,
				'Count' => $Count,
				'Termin' => $termin->Termin,
				'POCode' => $invoicepisah->POCode,
				'Abjad' => $invoicepisah->Abjad,
			]);
		}
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
		$history->History = 'Extend Transaksi Sewa on Invoice '.$invoice->Invoice;
		$history->save();
		
		Session::flash('message', 'Extend is successful!');

		return redirect()->route('transaksi.index');
	}
	
	public function ExtendDelete(Request $request)
	{
		$invoice = Invoice::find($request->id);

		Periode::where('Reference', $invoice->Reference)->where('Periode', $invoice->Periode)->where('Deletes', 'Extend')->delete();
		DB::statement('ALTER TABLE periode auto_increment = 1;');
		
		Invoice::destroy($invoice->id);
		DB::statement('ALTER TABLE invoice auto_increment = 1;');
		
		InvoicePisah::where('Periode', $invoice->Periode)
		->where('Reference', $invoice->Reference)
		->delete();
		DB::statement('ALTER TABLE invoicepisah auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete Extend Transaksi on Transaksi '.$invoice->Invoice;
		$history->save();
		
		Session::flash('message', 'Delete extend transaksi is successful!');

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
		
		if(in_array("claimcreate", $this->access)){
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
		->groupBy('isisjkirim.SJKir')
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
		
		if(in_array("claimcreate2", $this->access)){
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
			$SJKir[] = $input['checkbox'][$key];
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
		->whereIn('isisjkirim.SJKir', $SJKir)
		->whereIn('periode.id', $maxperiodeid)
		->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
		->groupBy('isisjkirim.Purchase')
		->orderBy('periode.id', 'asc')
		->get();
		
		if(in_array("claimcreate3", $this->access)){
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
		
		DB::select('CALL insert_claim2');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Transaksi Claim on claim '.str_pad($request['invoiceid'], 5, "0", STR_PAD_LEFT);
		$history->save();

		Session::forget('Tgl');
		Session::forget('Reference');
		
		return redirect()->route('transaksi.index');
	}
	
	public function ClaimDelete(Request $request)
	{
		$invoice = Invoice::find($request->id);
		
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
		DB::statement('ALTER TABLE periode auto_increment = 1;');
		
		TransaksiClaim::whereIn('Claim', $claim)->delete();
		DB::statement('ALTER TABLE transaksiclaim auto_increment = 1;');
		
		Invoice::destroy($invoice->id);
		DB::statement('ALTER TABLE invoice auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete Transaksi Claim on claim '.$invoice->Invoice;
		$history->save();
		
		Session::flash('message', 'Delete claim is successful!');

		return redirect()->route('transaksi.index');
	}
}
