<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Customer;
use Session;
use DB;

class CustomerController extends Controller
{
    public function index()
    {
  		$customer = Customer::all();

    	return view('pages.customer.indexs')
  		->with('customers', $customer)
      ->with('top_menu_sel', 'menu_customer')
  		->with('page_title', 'Customer')
  		->with('page_description', 'Index');
    }

    public function create()
    {
      $customer = Customer::orderby('id', 'desc')
      ->first();
      
    	return view('pages.customer.create')
      ->with('customer', $customer)
      ->with('top_menu_sel', 'menu_customer')
      ->with('page_title', 'Customer')
      ->with('page_description', 'Create');
    }

    public function store(Request $request)
    {

    	//$product = new Product;
    	//$product->name = $request->name;
    	//$product->price = $request->price;
    	//$product->save();

    	$inputs = $request->all();

    	//return $inputs;

    	$customer = Customer::Create($inputs);

    	return redirect()->route('customer.index');
    }

    public function show($id)
    {
    	$customer = Customer::find($id);

    	return view('pages.customer.show')
      ->with('customer', $customer)
      ->with('top_menu_sel', 'menu_customer')
      ->with('page_title', 'Customer')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
    	$customer = Customer::find($id);

    	return view('pages.customer.edit')
      ->with('customer', $customer)
      ->with('top_menu_sel', 'menu_customer')
      ->with('page_title', 'Customer')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
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
    	$customer->save();

    	return redirect()->route('customer.show', $id);
    }

    public function destroy($id)
    {
    	Customer::destroy($id);
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('customer.index');
    }
}
