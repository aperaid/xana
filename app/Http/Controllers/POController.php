<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PO;
use DB;

class POController extends Controller
{
    public function create()
    {
      $po = DB::table('po')->orderby('id', 'desc')->first();
      
    	return view('pages.po.create')
      ->with('po', $po)
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Item');
    }
/*
    public function store(Request $request)
    {
    	
    	$inputs = $request->all();

    	$reference = Reference::Create($inputs);

    	return redirect()->route('reference.index');
    }

    public function show($id)
    {
    	$detail = DB::table('pocustomer')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->select('pocustomer.*', 'project.*', 'customer.*', 'customer.Alamat as custalamat', 'project.Alamat as projalamat')
      ->where('pocustomer.id', $id)
      ->first();
      
      $purchase = DB::table('transaksi')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->where('transaksi.reference', $detail -> Reference)
      ->get();
      
      $sjkircheck = DB::table('transaksi')
      ->where('transaksi.Reference', $detail -> Reference)
      ->count();
      
      $sjkemcheck = DB::table('transaksi')
      ->where('transaksi.Reference', $detail -> Reference)
      ->count();
      
      $pocheck = DB::table('sjkirim')
      ->where('sjkirim.Reference', $detail -> Reference)
      ->count();

    	return view('pages.reference.show')
      ->with('detail', $detail)
      ->with('purchases', $purchase)
      ->with('sjkircheck', $sjkircheck)
      ->with('sjkemcheck', $sjkemcheck)
      ->with('pocheck', $pocheck)
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'View');
    }
    
    public function edit($id)
    {
    	$project = Project::find($id);

    	return view('pages.project.edit')
      ->with('project', $project)
      ->with('page_title', 'Project')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
    	$project = Project::find($id);

    	$project->PCode = $request->PCode;
    	$project->Project = $request->Project;
      $project->Alamat = $request->Alamat;
    	$project->CCode = $request->CCode;
    	$project->save();

    	return redirect()->route('project.show', $id);
    }

    public function destroy($id)
    {
    	Project::destroy($id);

    	return redirect()->route('project.index');
    }*/
}
