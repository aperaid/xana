<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Supplier;
use App\Permintaan;
use App\History;
use Session;
use DB;
use Auth;

class SupplierController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Administrator'||Auth::user()->access=='PPNAdmin'||Auth::user()->access=='NonPPNAdmin'||Auth::user()->access=='Purchasing'||Auth::user()->access=='SuperPurchasing'))
				$this->access = array("index", "create", "show", "edit");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
	public function index(){
			$suppliers = Supplier::all();
	
		if(in_array("index", $this->access)){
			return view('pages.supplier.indexs')
			->with('url', 'supplier')
			->with('suppliers', $suppliers)
			->with('top_menu_sel', 'menu_supplier')
			->with('page_title', 'Supplier')
			->with('page_description', 'Index');
		}else
			return redirect()->back();
	}

	public function CreateSupplier(){
		$supplier = Supplier::select([
			DB::raw('MAX(supplier.id) AS maxid')
		])
		->first();
		
		if(in_array("create", $this->access)){
			return view('pages.supplier.create')
			->with('url', 'supplier')
			->with('supplier', $supplier)
			->with('top_menu_sel', 'menu_supplier')
			->with('page_title', 'Supplier')
			->with('page_description', 'Create');
		}else
			return redirect()->back();
	}

	public function StoreSupplier(Request $request){
		//Validation
		$this->validate($request, [
			'SCode'=>'required|unique:supplier',
			'Company'=>'required'
		], [
			'SCode.required' => 'The Supplier Code field is required.',
			'SCode.unique' => 'The Supplier Code has already been taken.',
			'Company.required' => 'The Company Name field is required.'
		]);

		$inputs = $request->all();
		$supplier = Supplier::Create($inputs);
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create supplier on SCode number '.$request['SCode'];
		$history->save();

		return redirect()->route('supplier.show', $request->id);
	}

	public function ShowSupplier($id){
		$supplier = Supplier::find($id);
		
		$checksup = Permintaan::where('SCode', $supplier->SCode)->count();
		if($checksup==0)
			$checksup = 0;
		else
			$checksup = 1;

		if(in_array("show", $this->access)){
			return view('pages.supplier.show')
			->with('url', 'supplier')
			->with('checksup', $checksup)
			->with('supplier', $supplier)
			->with('top_menu_sel', 'menu_supplier')
			->with('page_title', 'Supplier')
			->with('page_description', 'View');
		}else
			return redirect()->back();
	}

	public function EditSupplier($id){
		$supplier = Supplier::find($id);

		if(in_array("edit", $this->access)){
			return view('pages.supplier.edit')
			->with('url', 'supplier')
			->with('supplier', $supplier)
			->with('top_menu_sel', 'menu_supplier')
			->with('page_title', 'Supplier')
			->with('page_description', 'Edit');
		}else
			return redirect()->back();
	}

	public function UpdateSupplier(Request $request){
		//Validation
		$this->validate($request, [
			'SCode'=>'required|unique:supplier,SCode,'.$request->SCode.',SCode',
			'Company'=>'required'
		], [
			'SCode.required' => 'The Supplier Code field is required.',
			'SCode.unique' => 'The Supplier Code has already been taken.',
			'Company.required' => 'The Company Name field is required.'
		]);
		
		$supplier = Supplier::find($request->id);

		$supplier->SCode = $request->SCode;
		$supplier->Company = $request->Company;
		$supplier->NPWP = $request->NPWP;
		$supplier->CompAlamat = $request->CompAlamat;
		$supplier->CompKota = $request->CompKota;
		$supplier->CompZip = $request->CompZip;
		$supplier->CompPhone = $request->CompPhone;
		$supplier->Fax = $request->Fax;
		$supplier->CompEmail = $request->CompEmail;
		$supplier->Supplier = $request->Supplier;
		$supplier->SupPhone = $request->SupPhone;
		$supplier->SupEmail = $request->SupEmail;
		$supplier->Supplier2 = $request->Supplier2;
		$supplier->SupPhone2 = $request->SupPhone2;
		$supplier->SupEmail2 = $request->SupEmail2;
		$supplier->Supplier3 = $request->Supplier3;
		$supplier->SupPhone3 = $request->SupPhone3;
		$supplier->SupEmail3 = $request->SupEmail3;
		$supplier->save();
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Update supplier on SCode number '.$request['SCode'];
		$history->save();

		return redirect()->route('supplier.show', $request->id);
	}

	public function DeleteSupplier(Request $request){
		Supplier::destroy($request->id);
		DB::statement('ALTER TABLE supplier auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete supplier on SCode number '.$request->SCode;
		$history->save();
		
		Session::flash('message', 'Supplier with SCode '.$request->SCode.' is deleted');
	}
}
