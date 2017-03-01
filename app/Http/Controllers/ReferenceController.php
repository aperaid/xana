<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Customer;
use App\Project;
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
use App\InvoicePisah;
use App\History;
use Session;
use DB;
use Auth;

class ReferenceController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Admin'||Auth::user()->access=='CUSTINVPPN'||Auth::user()->access=='CUSTINVNONPPN'))
				$this->access = array("index", "create", "show", "edit");
			else if(Auth::check()&&(Auth::user()->access=='POINVPPN'||Auth::user()->access=='POINVNONPPN'))
				$this->access = array("index", "create", "show", "edit");
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
		if(Auth::user()->access == 'Admin'){
			$reference = Reference::select([DB::raw('SUM(transaksi.Amount*transaksi.Quantity) AS Price'), 'pocustomer.*', 'project.*', 'customer.*', 'pocustomer.id as Id'])
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->leftJoin('transaksi', 'pocustomer.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->groupBy('pocustomer.Reference')
			->get();
		}else{
			$reference = Reference::select([DB::raw('SUM(transaksi.Amount*transaksi.Quantity) AS Price'), 'pocustomer.*', 'project.*', 'customer.*', 'pocustomer.id as Id'])
			->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->leftJoin('transaksi', 'pocustomer.Reference', '=', 'transaksi.Reference')
			->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
			->where('PPN', $this->PPNNONPPN)
			->groupBy('pocustomer.Reference')
			->get();
		}
		
		if(in_array("index", $this->access)){
			return view('pages.reference.indexs')
			->with('url', 'reference')
			->with('reference', $reference)
			->with('top_menu_sel', 'menu_referensi')
			->with('page_title', 'Purchase Order')
			->with('page_description', 'Index');
		}else
      return redirect()->back();
	}

	public function create()
	{
		$reference = Reference::select([
			DB::raw('MAX(pocustomer.id) AS maxid')
		])
		->first();
		$customer_id = Customer::max('id')+1;
		$project_id = Project::max('id')+1;
		
		if(in_array("create", $this->access)){
			return view('pages.reference.create')
			->with('url', 'reference')
			->with('reference', $reference)
			->with('customer_id', $customer_id)
			->with('project_id', $project_id)
			->with('top_menu_sel', 'menu_referensi')
			->with('page_title', 'Purchase Order')
			->with('page_description', 'Create');
		}else
			return redirect()->back();
	}

	public function store(Request $request)
	{
		$inputs = $request->all();
		
		$Reference = Reference::Create([
			'id' => $request['id'],
			'Reference' => $request['Reference'],
			'Tgl' => $request['Tgl'],
			'PCode' => $request['PCode'],
			'PPN' => $request['PPN'],
			'Transport' => str_replace(".","",substr($request->Transport, 3)),
			'Discount' => $request['Discount'],
			//'PPNT' => $request['PPNT'],
		]);

		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Reference on Reference '.$request['Reference'];
		$history->save();

		return redirect()->route('reference.show', $request['id']);
	}
	
	public function StoreCustomerProject(Request $request)
	{
		if($request['CCode2']!=''){
			$this->validate($request, [
				'Company'=>'required'
			]);
			$is_exist = Customer::where('CCode', $request->CCode2)->first();
			if(isset($is_exist->CCode)){
				$request->session()->flash('error', 'Customer with CCode '.strtoupper($request->CCode).' is already exist!');
			}else{
				$customer = Customer::Create([
					'id' => $request['customerid'],
					'CCode' => strtoupper($request['CCode2']),
					'Company' => strtoupper($request['Company']),
					'Customer' => strtoupper($request['Customer']),
					'CompAlamat' => $request['CompAlamat'],
					'CompZip' => $request['CompZip'],
					'CompKota' => $request['CompKota'],
					'CompPhone' => $request['CompPhone'],
					'CompEmail' => $request['CompEmail'],
					'CustPhone' => $request['CustPhone'],
					'CustEmail' => $request['CustEmail'],
					'Fax' => $request['Fax'],
					'NPWP' => $request['NPWP'],
				]);
				$request->session()->flash('message', 'Customer and Project has been successfully added with PCode '. strtoupper($request['PCode']));
			}
		}

		$is_exist = Project::where('PCode', $request->PCode)->first();
		if(isset($is_exist->PCode)){
			$request->session()->flash('error', 'Project with PCode '.strtoupper($request->PCode).' is already exist!');
		}else{
			$project = Project::Create([
				'id' => $request['projectid'],
				'PCode' => strtoupper($request['PCode']),
				'Project' => strtoupper($request['Project']),
				'Sales' => $request['Sales'],
				'ProjAlamat' => $request['ProjAlamat'],
				'ProjZip' => $request['ProjZip'],
				'ProjKota' => $request['ProjKota'],
				'CCode' => strtoupper($request['CCode']),
			]);
			$request->session()->flash('message', 'Project has been successfully added with PCode '. strtoupper($request['PCode']));
		}
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create customer on CCode number '.$request['CCode'];
		$history->save();
	}
	
	public function EditTransportInvoice(Request $request)
	{
		$reference = Reference::find($request->editreferenceid);

		$reference->PPNT = $request->PPNT;
		$reference->INVP = $request->INVP;
		$reference->save();
		
		$request->session()->flash('message', 'Edit Success');
	}

	public function show($id)
	{
		$detail = Reference::leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
		->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
		->select('pocustomer.id as pocusid', 'pocustomer.*', 'project.*', 'customer.*')
		->where('pocustomer.id', $id)
		->first();
		
		$purchase = Transaksi::leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
		->leftJoin('po', 'transaksi.POCode', '=', 'po.POCode')
		->leftJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
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
		
		$delcheck = SJKirim::where('sjkirim.Reference', $detail -> Reference)
		->count();
		if($delcheck == 0){
			$delcheck = 0;
		}else{
			$delcheck = 1;
		}
		
		$pocheck = po::leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
		->where('transaksi.Reference', $detail -> Reference)
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
		
		if(in_array("show", $this->access)){
			return view('pages.reference.show')
			->with('url', 'reference')
			->with('detail', $detail)
			->with('purchases', $purchase)
			->with('sjkircheck', $sjkircheck)
			->with('sjkemcheck', $sjkemcheck)
			->with('delcheck', $delcheck)
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
		}else
			return redirect()->back();
	}

	public function edit($id)
	{
		$reference = Reference::find($id);

		if(in_array("edit", $this->access)){
			return view('pages.reference.edit')
			->with('url', 'reference')
			->with('reference', $reference)
			->with('top_menu_sel', 'menu_referensi')
			->with('page_title', 'Reference')
			->with('page_description', 'Edit');
		}else
			return redirect()->back();
	}

	public function update(Request $request, $id)
	{
		$reference = Reference::find($id);

		$reference->Reference = $request->Reference;
		$reference->Tgl = $request->Tgl;
		$reference->PCode = $request->PCode;
		$reference->Transport = str_replace(".","",substr($request->Transport, 3));
    $reference->Discount = $request->Discount;
		$reference->save();
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Update Reference on Reference '.$request['Reference'];
		$history->save();

		return redirect()->route('reference.show', $id);
	}

	public function destroy(Request $request, $id)
	{
		$reference = Reference::find($id);
		
		$transaksi = Transaksi::where('transaksi.Reference', $reference->Reference);
		$transaksiid = $transaksi->pluck('id');
		$invoice = Invoice::where('Invoice.Reference', $reference->Reference);
		$invoiceid = $invoice->pluck('id');
		$po = PO::whereIn('po.POCode', $transaksi->pluck('POCode'));
		$poid = $po->pluck('id');
		
		Invoice::whereIn('id', $invoiceid)->delete();
		DB::statement('ALTER TABLE invoice auto_increment = 1;');
		
		InvoicePisah::whereIn('id', $invoiceid)->delete();
		DB::statement('ALTER TABLE invoicepisah auto_increment = 1;');
		
		Transaksi::whereIn('id', $transaksiid)->delete();
		DB::statement('ALTER TABLE transaksi auto_increment = 1;');
		
		PO::whereIn('id', $poid)->delete();
		DB::statement('ALTER TABLE po auto_increment = 1;');
		
		Reference::destroy($id);
		DB::statement('ALTER TABLE pocustomer auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete Reference on Reference '.$reference->Reference;
		$history->save();

		Session::flash('message', 'Delete is successful!');

		return redirect()->route('reference.index');
	}
}
