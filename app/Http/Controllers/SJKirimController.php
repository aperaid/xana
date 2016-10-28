<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Periode;
use App\SJKirim;
use App\IsiSJKirim;
use Session;
use DB;

class SJKirimController extends Controller
{
    public function index()
    {
      $sum = IsiSJKirim::select([
          'isisjkirim.SJKir',
          DB::raw('sum(isisjkirim.QTertanda) AS qttd')
        ])
        ->groupBy('isisjkirim.SJKir');
      $sjkirim = SJKirim::select([
        'qttd',
        'sjkirim.*',
        'project.Project',
        'customer.Customer',
      ])
      ->leftJoin('pocustomer', 'sjkirim.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->leftJoin(DB::raw(sprintf( '(%s) AS T1', $sum->toSql() )), function($join){
          $join->on('T1.SJKir', '=', 'sjkirim.SJKir');
        })
      ->orderBy('sjkirim.id', 'asc')
      ->get();

    	return view('pages.sjkirim.indexs')
      ->with('sjkirims', $sjkirim)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
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
      $sjkirim = SJKirim::find($id);

      $isisjkirims = IsiSJKirim::select([
        'isisjkirim.*',
        'periode.Periode',
        'transaksi.*',
        'project.*',
        'customer.*'
      ])
      ->leftJoin('periode', 'isisjkirim.IsiSJKir', '=', 'periode.IsiSJKir')
      ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->groupBy('periode.IsiSJKir')
      ->orderBy('isisjkirim.id', 'asc')
      ->get();
      
      $isisjkirim = $isisjkirims->first();
      
      $jumlah = $isisjkirims->sum('QTertanda');

      $qttdcheck = Periode::select([
        DB::raw('max(periode.Periode) as found')
      ])
      ->leftJoin('isisjkirim', 'periode.IsiSJKir', '=', 'isisjkirim.IsiSJKir')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->first();
      if($qttdcheck->found > 1){
        $qttdcheck = 1;
      }else{
        $qttdcheck = 0;
      }
      
    	return view('pages.sjkirim.show')
      ->with('sjkirim', $sjkirim)
      ->with('isisjkirim', $isisjkirim)
      ->with('isisjkirims', $isisjkirims)
      ->with('jumlah', $jumlah)
      ->with('qttdcheck', $qttdcheck)
      ->with('top_menu_sel', 'menu_sjkirim')
      ->with('page_title', 'Surat Jalan Kirim')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
    	$sjkirim = SJKirim::find($id);
      
      $isisjkirims = IsiSJKirim::select([
        'isisjkirim.*',
        'sjkirim.Tgl',
        'transaksi.*',
        'project.Project',
      ])
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->leftJoin('transaksi', 'isisjkirim.Purchase', '=', 'transaksi.Purchase')
      ->leftJoin('pocustomer', 'transaksi.Reference', '=', 'pocustomer.Reference')
      ->leftJoin('project', 'pocustomer.PCode', '=', 'project.PCode')
      ->where('isisjkirim.SJKir', $sjkirim->SJKir)
      ->orderBy('isisjkirim.id', 'asc')
      ->get();
      
    	return view('pages.project.edit')
      ->with('project', $project)
      ->with('page_title', 'Project')
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
    }

    public function destroy($id)
    {
    	Project::destroy($id);
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('project.index');
    }*/
}
