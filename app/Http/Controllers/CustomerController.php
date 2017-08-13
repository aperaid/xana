<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Customer;
use App\Project;
use App\History;
use Session;
use DB;
use Auth;

class CustomerController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='SuperAdmin'||Auth::user()->access=='PPNAdmin'||Auth::user()->access=='NonPPNAdmin'||Auth::user()->access=='Purchasing'||Auth::user()->access=='SuperPurchasing'))
				$this->access = array("index", "create", "show", "edit");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
	public function index(){
		if(Auth::user()->access == 'SuperAdmin'){
			$customer = Customer::all();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$customer = Customer::where('PPN', 1)
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$customer = Customer::where('PPN', 0)
			->get();
		}
	
		if(in_array("index", $this->access)){
			return view('pages.customer.indexs')
			->with('url', 'customer')
			->with('customers', $customer)
			->with('top_menu_sel', 'menu_customer')
			->with('page_title', 'Customer')
			->with('page_description', 'Index');
		}else
			return redirect()->back();
	}

	public function create(){
		$customer = Customer::select([
			DB::raw('MAX(customer.id) AS maxid')
		])
		->first();
		
		if(in_array("create", $this->access)){
			return view('pages.customer.create')
			->with('url', 'customer')
			->with('customer', $customer)
			->with('top_menu_sel', 'menu_customer')
			->with('page_title', 'Customer')
			->with('page_description', 'Create');
		}else
			return redirect()->back();
	}

	public function store(Request $request){

		//$product = new Product;
		//$product->name = $request->name;
		//$product->price = $request->price;
		//$product->save();
		
		//Validation
		$this->validate($request, [
			'CCode'=>'required|unique:customer',
			'Company'=>'required'
		]);

		$inputs = $request->all();
		$customer = Customer::Create($inputs);
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create customer on CCode number '.$request['CCode'];
		$history->save();

		return redirect()->route('customer.index');
	}

	public function show($id){
		$customer = Customer::find($id);
		
		$checkcust = Project::where('CCode', $customer->CCode)->count();
		if($checkcust==0)
			$checkcust = 0;
		else
			$checkcust = 1;

		if(in_array("show", $this->access)){
			return view('pages.customer.show')
			->with('url', 'customer')
			->with('checkcust', $checkcust)
			->with('customer', $customer)
			->with('top_menu_sel', 'menu_customer')
			->with('page_title', 'Customer')
			->with('page_description', 'View');
		}else
			return redirect()->back();
	}

	public function edit($id){
		$customer = Customer::find($id);

		if(in_array("edit", $this->access)){
			return view('pages.customer.edit')
			->with('url', 'customer')
			->with('customer', $customer)
			->with('top_menu_sel', 'menu_customer')
			->with('page_title', 'Customer')
			->with('page_description', 'Edit');
		}else
			return redirect()->back();
	}

	public function update(Request $request, $id){
		//Validation
		$this->validate($request, [
			'CCode'=>'required|unique:customer,id,'.$request->id.',id',
			'Company'=>'required'
		]);
		
		$customer = Customer::find($id);

		$customer->CCode = $request->CCode;
		$customer->Company = $request->Company;
		$customer->NPWP = $request->NPWP;
		$customer->CompAlamat = $request->CompAlamat;
		$customer->CompKota = $request->CompKota;
		$customer->CompZip = $request->CompZip;
		$customer->CompPhone = $request->CompPhone;
		$customer->Fax = $request->Fax;
		$customer->CompEmail = $request->CompEmail;
		$customer->Customer = $request->Customer;
		$customer->CustPhone = $request->CustPhone;
		$customer->CustEmail = $request->CustEmail;
		$customer->Customer2 = $request->Customer2;
		$customer->CustPhone2 = $request->CustPhone2;
		$customer->CustEmail2 = $request->CustEmail2;
		$customer->Customer3 = $request->Customer3;
		$customer->CustPhone3 = $request->CustPhone3;
		$customer->CustEmail3 = $request->CustEmail3;
		$customer->save();
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Update customer on CCode number '.$request['CCode'];
		$history->save();

		return redirect()->route('customer.show', $id);
	}

	public function destroy(Request $request, $id){
		Customer::destroy($id);
		DB::statement('ALTER TABLE customer auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete customer on CCode number '.$request['CCode'];
		$history->save();
		
		Session::flash('message', 'Delete is successful!');

		return redirect()->route('customer.index');
	}
}
