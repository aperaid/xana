<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SJKembali;
use App\IsiSJKembali;
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
        DB::raw('sum(isisjkembali.QTertanda) as QTertanda2'),
        DB::raw('sum(isisjkembali.QTerima) as QTerima2'),
        'isisjkembali.*',
        'isisjkirim.QSisaKem',
        'sjkirim.Tgl',
        'sjkirim.Reference',
        'transaksi.Barang',
        'project.Project',
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
      ->with('sjkembali', $sjkembali)
      ->with('isisjkembali', $isisjkembali)
      ->with('isisjkembalis', $isisjkembalis)
      ->with('qtrimacheck', $qtrimacheck)
      ->with('top_menu_sel', 'menu_sjkembali')
      ->with('page_title', 'Surat Jalan Kembali')
      ->with('page_description', 'View');
    }
/*
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
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('project.index');
    }*/
}
