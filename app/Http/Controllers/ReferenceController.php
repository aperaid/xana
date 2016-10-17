<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Reference;
use DB;
class ReferenceController extends Controller
{
    public function index()
    {
    $reference = Reference::join('project', 'pocustomer.PCode', '=', 'project.PCode')
            ->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
            ->get();

         
    	return view('pages.reference.indexs')
      ->with('reference', $reference)
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Index');
    }

    public function create()
    {
      $reference = DB::table('pocustomer')->orderby('id', 'desc')->first();
      
    	return view('pages.reference.create')
      ->with('reference', $reference)
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Create');
    }

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
      
      $sjkircheck = 0;
      $kirexist = DB::table('transaksi')
      ->where('transaksi.Reference', $detail -> Reference)
      ->count();
      $kirfound = DB::table('transaksi')
      ->selectRaw('SUM(QSisaKirInsert) as kirfound')
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
      $kemexist = DB::table('transaksi')
      ->where('transaksi.Reference', $detail -> Reference)
      ->count();
      $kemfound = DB::table('transaksi')
      ->selectRaw('SUM(QSisaKem) as kemfound')
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
      
      $pocheck = DB::table('sjkirim')
      ->where('sjkirim.Reference', $detail -> Reference)
      ->count();
      if($pocheck == 0){
        $pocheck = 0;
      }else{
        $pocheck = 1;
      }
      
      $po = DB::table('po')
      ->leftJoin('transaksi', 'po.POCode', '=', 'transaksi.POCode')
      ->where('transaksi.Reference', $detail -> Reference)
      ->groupBy('po.POCode')
      ->get();

      $sjkirim = DB::table('isisjkirim')
      ->leftJoin('sjkirim', 'isisjkirim.SJKir', '=', 'sjkirim.SJKir')
      ->where('sjkirim.Reference', $detail -> Reference)
      ->groupBy('sjkirim.SJKir')
      ->get();
      
      $sjkembali = DB::table('isisjkembali')
      ->leftJoin('sjkembali', 'isisjkembali.SJKem', '=', 'sjkembali.SJKem')
      ->where('sjkembali.Reference', $detail -> Reference)
      ->groupBy('sjkembali.SJKem')
      ->get();
      
      $maxid = DB::table('periode')
      ->select([
        'periode.Reference',
        'periode.IsiSJKir',
        DB::raw('MAX(periode.id) AS maxid')
      ])
      ->whereRaw('(periode.Deletes = "Sewa" OR periode.Deletes = "Extend")')
      ->groupBy('periode.IsiSJKir')
      ->orderBy('periode.id', 'asc');
      
      $sewa = DB::table('periode')
      ->select([
        'invoice.Invoice',
        'periode.*',
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
      ->groupBy('invoice.Reference','invoice.Periode')
      ->get();
      
      $jual = DB::table('periode')
      ->select([
        'pocustomer.Reference',
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
      ->groupBy('periode.Reference','periode.Periode')
      ->get();
      
      $transaksiclaim = DB::table('periode')
      ->select([
        'periode.Reference',
        'periode.Claim',
        'periode.Periode',
        DB::raw('MAX(periode.Periode) AS periodeclaim')
      ])
      ->whereRaw('(periode.Deletes = "Claim")');
      
      $transaksiextend = DB::table('periode')
      ->select([
        'periode.Reference',
        DB::raw('MAX(periode.Periode) AS periodeextend')
      ])
      ->whereRaw('(periode.Deletes = "Extend" OR periode.Deletes = "Sewa")');
      
      $claim = DB::table('transaksiclaim')
      ->select([
        'transaksiclaim.*',
        'periodeclaim',
        'periodeextend',
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
        ->on('invoice.Periode', '=', 'transaksiclaim.Periode')
        ->on('invoice.JSC', '=', 'transaksi.JS');
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
      ->groupBy('transaksiclaim.Periode')
      ->orderBy('transaksiclaim.id', 'asc')
      ->get();
      
    	return view('pages.reference.show')
      ->with('detail', $detail)
      ->with('purchases', $purchase)
      ->with('sjkircheck', $sjkircheck)
      ->with('sjkemcheck', $sjkemcheck)
      ->with('pocheck', $pocheck)
      ->with('pos', $po)
      ->with('sjkirims', $sjkirim)
      ->with('sjkembalis', $sjkembali)
      ->with('sewas', $sewa)
      ->with('juals', $jual)
      ->with('claims', $claim)
      ->with('page_title', 'Purchase Order')
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

    	return redirect()->route('project.index');
    }*/
}
