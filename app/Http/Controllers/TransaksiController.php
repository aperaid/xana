<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Periode;
use App\TransaksiClaim;
use Session;
use DB;

class TransaksiController extends Controller
{
    public function index()
    {
      $maxid = Periode::select([
        'periode.Reference',
        'periode.IsiSJKir',
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('periode.IsiSJKir')
      ->orderBy('periode.id', 'asc');
      
      $transaksis = Periode::select([
        'invoice.Invoice',
        'periode.*',
        'isisjkirim.SJKir',
        'project.Project',
        'customer.Customer',
        'maxid',
      ])
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'periode.IsiSJKir')
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
      ->whereRaw('periode.Deletes = "Sewa" OR periode.Deletes = "Extend"')
      ->groupBy('invoice.Reference', 'invoice.Periode')
      ->get();
      
      $transaksij = Periode::select([
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
      ->where('periode.Deletes', 'Jual')
      ->groupBy('periode.Reference', 'periode.Periode')
      ->get();
      
      $T1 = Periode::select([
        'periode.Reference',
        'periode.Claim',
        'periode.Periode',
        DB::raw('MAX(periode.id) AS periodeclaim')
      ])
      ->whereRaw('periode.Deletes = "Claim"');
      
      $T2 = Periode::select([
        'periode.Reference',
        DB::raw('MAX(periode.id) AS periodeextend')
      ])
      ->whereRaw('periode.Deletes = "Sewa" OR periode.Deletes = "Extend"');
      
      $transaksic = TransaksiClaim::select([
        'periodeclaim',
        'periodeextend',
        'transaksiclaim.*',
        'invoice.Invoice',
        'periode.Reference',
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
      ->groupBy('transaksiclaim.Periode')
      ->orderBy('transaksiclaim.id', 'asc')
      ->get();
      
      return view('pages.transaksi.indexs')
      ->with('transaksiss', $transaksis)
      ->with('transaksijs', $transaksij)
      ->with('transaksics', $transaksic)
      ->with('top_menu_sel', 'menu_transaksi')
      ->with('page_title', 'Transaksi')
      ->with('page_description', 'Index');
    }
/*

$transaksi = Invoice::select([
        'invoice.Reference',
        'invoice.Invoice',
        'invoice.Periode',
        'invoice.Tgl',
        'project.Project',
        'customer.Company',
      ])
      ->leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->where('invoice.JSC', 'Sewa')
      ->whereExists(function($query)
        {
          $query->select('periode.Reference')
          ->from('periode')
          ->whereRaw('invoice.Reference = periode.Reference AND periode.Deletes = "Sewa"');
        })
      ->groupBy('invoice.Reference', 'invoice.Periode')
      ->get();

$transaksi = TransaksiClaim::select([
        'transaksiclaim.*',
        'invoice.Invoice',
        'periode.Reference',
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
      ->groupBy('transaksiclaim.Periode')
      ->orderBy('transaksiclaim.id', 'asc')
      ->get();
    public function create()
    {
      $project = Project::orderby('id', 'desc')
      ->first();
      
    	return view('pages.project.create')
      ->with('project', $project)
      ->with('top_menu_sel', 'menu_project')
      ->with('page_title', 'Project')
      ->with('page_description', 'Create');
    }

    public function store(Request $request)
    {
    	
    	$inputs = $request->all();

    	$project = Project::Create($inputs);

    	return redirect()->route('project.index');
    }

    public function show($id)
    {
    	$project = Project::find($id);

    	return view('pages.project.show')
      ->with('project', $project)
      ->with('top_menu_sel', 'menu_project')
      ->with('page_title', 'Project')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
    	$project = Project::find($id);

    	return view('pages.project.edit')
      ->with('project', $project)
      ->with('top_menu_sel', 'menu_project')
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
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('project.index');
    }*/
}
