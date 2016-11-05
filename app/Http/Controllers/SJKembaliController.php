<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SJKembali;
use App\IsiSJKembali;
use App\Periode;
use Session;
Use DB;

class SJKembaliController extends Controller
{
    public function index()
    {
      $sum = IsiSJKembali::select([
          'isisjkembali.SJKem',
          DB::raw('sum(isisjkembali.QTertanda) AS qtrima')
        ])
        ->groupBy('isisjkembali.SJKem');
      $sjkembali = SJKembali::select([
        'qtrima',
        'sjkembali.*',
        'project.Project',
        'customer.Customer',
      ])
      ->leftJoin('pocustomer', 'sjkembali.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->leftJoin(DB::raw(sprintf( '(%s) AS T1', $sum->toSql() )), function($join){
          $join->on('T1.SJKem', '=', 'sjkembali.SJKem');
        })
      ->orderBy('sjkembali.id', 'asc')
      ->get();

    	return view('pages.sjkembali.indexs')
      ->with('url', 'sjkembali')
      ->with('sjkembalis', $sjkembali)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'Index');
    }
/*
    public function create()
    {
    	return view('pages.project.create')
      ->with('page_title', 'Project')
      ->with('page_description', 'Create');
    }

    public function store(Request $request)
    {
    	
    	$inputs = $request->all();

    	$project = Project::Create($inputs);

    	return redirect()->route('project.index');
    }*/

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
      if($qtrimacheck->found = 0){
        $qtrimacheck = 0;
      }else{
        $qtrimacheck = 1;
      }
      
    	return view('pages.sjkembali.show')
      ->with('url', 'sjkembali')
      ->with('sjkembali', $sjkembali)
      ->with('isisjkembali', $isisjkembali)
      ->with('isisjkembalis', $isisjkembalis)
      ->with('qtrimacheck', $qtrimacheck)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
      $sjkembali = SJKembali::find($id);
      
      $TglMin = Periode::select([
        'periode.S',
      ])
      ->where('periode.Reference', $sjkembali->Reference)
      ->where('periode.Deletes', 'Sewa')
      ->first();
      
      $TglMax = Periode::select([
        'periode.E',
      ])
      ->where('periode.Reference', $sjkembali->Reference)
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->orderBy('Periode.id', 'desc')
      ->first();
      
      $maxperiode = Periode::select([
        DB::raw('MAX(periode.Periode) AS maxper'),
      ])
      ->where('periode.Reference', $sjkembali->SJKem)
      ->first();
      
      $isisjkembalis = IsiSJKembali::select([
        DB::raw('sum(isisjkembali.QTertanda) as QTertanda2'),
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
      
      $isisjkembali2s = IsiSJKembali::select([
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

    	return view('pages.sjkembali.edit')
      ->with('url', 'sjkembali')
      ->with('sjkembali', $sjkembali)
      ->with('TglMin', $TglMin)
      ->with('TglMax', $TglMax)
      ->with('maxperiode', $maxperiode)
      ->with('isisjkembalis', $isisjkembalis)
      ->with('isisjkembali2s', $isisjkembali2s)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'Edit');
    }
/*
    public function update(Request $request, $id)
    {
    	$project = Project::find($id);

    	$project->PCode = $request->PCode;
    	$project->Project = $request->Project;
      $project->Alamat = $request->Alamat;
    	$project->CCode = $request->CCode;
    	$project->save();

    	return redirect()->route('project.show', $id);
    }*/
  
    public function getQTerima()
    { 
      $id = Input::get('id');
      
    	$sjkembali = SJKembali::find($id);
      
      $isisjkembalis = IsiSJKembali::select([
        DB::raw('sum(isisjkembali.QTertanda) as QTertanda2'),
        DB::raw('sum(isisjkembali.QTerima) as QTerima2'),
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
      ->first();
      
      $x = 1;
      
    	return view('pages.sjkembali.qterima')
      ->with('url', 'sjkembali')
      ->with('sjkembali', $sjkembali)
      ->with('isisjkembalis', $isisjkembalis)
      ->with('QTerima2', $QTerima2)
      ->with('Tgl', $Tgl)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'QTerima');
    }
    
/*
    public function destroy($id)
    {
    	Project::destroy($id);
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('project.index');
    }*/
}
