<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PO;
use App\Transaksi;
use App\Reference;
use App\Invoice;
use App\History;
use Session;
use DB;
use Auth;

class POController extends Controller
{
    public function create()
    {
      $po = PO::orderby('id', 'desc')
      ->first();
      
      $transaksi = Transaksi::orderby('id', 'desc')
      ->first();
      
      $id = Input::get('id');
      
      $reference = Reference::where('pocustomer.id', $id)
      ->first();

      return view('pages.po.create')
      ->with('url', 'po')
      ->with('po', $po)
      ->with('transaksi', $transaksi)
      ->with('id', $id)
      ->with('reference', $reference)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Item');
    }

    public function store(Request $request)
    {
      $id = $request['id'];
      
      $po = PO::Create([
        'id' => $request['poid'],
        'POCode' => $request['POCode'],
        'Tgl' => $request['Tgl'],
        'Catatan' => $request['Catatan'],
        'Transport' => str_replace(".","",substr($request->Transport, 3)),
      ]);

      $input = Input::all();
      $transaksis = $input['transaksiid'];
      foreach ($transaksis as $key => $transaksis)
      {
        $transaksis = new Transaksi;
        $transaksis->id = $input['transaksiid'][$key];
        $transaksis->Purchase = $input['Purchase'][$key];
        $transaksis->JS = $input['JS'][$key];
        $JSC[] = $input['JS'][$key];
        $transaksis->Barang = $input['Barang'][$key];
        $transaksis->Quantity = $input['Quantity'][$key];
        $transaksis->QSisaKirInsert = $input['Quantity'][$key];
        $transaksis->QSisaKir = $input['Quantity'][$key];
        $transaksis->Amount = $input['Amount'][$key];
        $transaksis->Reference = $input['Reference'];
        $transaksis->POCode = $input['POCode'];
        $transaksis->save();
      }
      
      /*$invoiceold = Invoice::where('Reference', $input['Reference'])
      ->first();
      if(is_null($invoiceold)){
        $invoice = $invoicenew->maxid+1;
      }else{
      if(count($JSC==2)){
        if(is_null($invoiceold)){
          $invoice = $invoicenew->maxid+1;
        }if($invoiceold == $JSC[0]){
          $invoice[] = [$invoiceold->id, $invoiceold->id+1];
        }else{
          $invoice = $invoiceold->id+1;
        }
        
        }
      }*/

      $JSC = array_unique($JSC);
      
      if(Auth::user()->access == 'POPPN'){
        $PPN = 1;
      }else{
        $PPN = 0;
      }
        
      $invoices = $JSC;
      foreach ($invoices as $key => $invoices)
      {
        $invoice = Invoice::select([
          DB::raw('MAX(id) AS maxid')
        ])
        ->first();
        
        $invoices = new Invoice;//Invoice::updateOrCreate(['Reference' => $request['Reference'], 'JSC' => $JSC[$key]]);
        $invoices->id = $invoice->maxid + 1;
        $invoices->Invoice = str_pad($invoice->maxid + 1, 5, "0", STR_PAD_LEFT);
        $invoices->JSC = $JSC[$key];
        $invoices->Tgl = $request['Tgl'];
        $invoices->Reference = $request['Reference'];
        $invoices->Periode = 1;
        $invoices->PPN = $PPN;
        $invoices->save();
        
        $duplicateRecords = Invoice::select([
          DB::raw('MAX(id) AS maxid')
        ])
        ->selectRaw('count(`Reference`) as `occurences`')
        ->where('Reference', $input['Reference'])
        ->groupBy('JSC', 'Periode')
        ->having('occurences', '>', 1)
        ->pluck('maxid');
        
        Invoice::whereIn('id', $duplicateRecords)->delete();
      }
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Create PO on POCode '.$request['POCode'];
      $history->save();
      
    	return redirect()->route('reference.show', $id);
    }

    public function show($id)
    {
      $po = PO::find($id);
      $transaksi = Transaksi::where('transaksi.POCode', $po -> POCode)
      ->get();
      
      $id = Reference::select('pocustomer.id')
      ->leftJoin('transaksi', 'pocustomer.Reference', '=', 'transaksi.Reference')
      ->where('transaksi.POCode', $po -> POCode)
      ->first();
      
      $poexist = Transaksi::from('transaksi')->distinct()
      ->rightJoin('isisjkirim', 'transaksi.Purchase', '=', 'isisjkirim.Purchase')
      ->where('transaksi.POCode', $po -> POCode)
      ->get();
      if($poexist != '[]'){
        $pocheck = 1;
      }else{
        $pocheck = 0;
      }
      
      return view('pages.po.show')
      ->with('url', 'po')
      ->with('po', $po)
      ->with('id', $id)
      ->with('transaksis', $transaksi)
      ->with('pocheck', $pocheck)
      ->with('poexist', $poexist)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Show');
    }

    public function edit($id)
    {
    	$po = PO::find($id);
      $transaksis = Transaksi::where('transaksi.POCode', $po -> POCode)
      ->get();
      $transaksi = $transaksis->first();
      
      $poitem = Transaksi::where('transaksi.POCode', $po -> POCode)
      ->first();
      $purchase = Transaksi::select([
        DB::raw('MAX(id) AS maxid')
      ])
      ->first();
      $purchase2 = Transaksi::select([
        DB::raw('MIN(id) AS minid')
      ])
      ->groupBy('POCode')
      ->orderBy('id', 'asc')
      ->first();
      if ($poitem['Id']==$purchase2['minid'])
      {
        $last_purchase = $purchase2['minid']-1;
      } else
      {
        $last_purchase = $purchase['maxid'];
      }

    	return view('pages.po.edit')
      ->with('url', 'po')
      ->with('po', $po)
      ->with('transaksi', $transaksi)
      ->with('transaksis', $transaksis)
      ->with('last_purchase', $last_purchase)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
    	$po = PO::find($id);
      $transaksi = Transaksi::where('transaksi.POCode', $po -> POCode);

    	$po->id = $request->poid;
    	$po->POCode = $request->POCode;
      $po->Tgl = $request->Tgl;
    	$po->Catatan = $request->Catatan;
      $po->Transport = str_replace(".","",substr($request->Transport, 3));
    	$po->save();
      
      $ids = $transaksi->pluck('id');
      Transaksi::whereIn('id', $ids)->delete();
      
      $input = Input::all();
      $transaksis = $input['transaksiid'];
      foreach ($transaksis as $key => $transaksis)
      {
        $transaksis = new Transaksi;
        $transaksis->id = $input['transaksiid'][$key];
        $transaksis->Purchase = $input['Purchase'][$key];
        $transaksis->JS = $input['JS'][$key];
        $JSC[] = $input['JS'][$key];
        $transaksis->Barang = $input['Barang'][$key];
        $transaksis->Quantity = $input['Quantity'][$key];
        $transaksis->QSisaKirInsert = $input['Quantity'][$key];
        $transaksis->QSisaKir = $input['Quantity'][$key];
        $transaksis->Amount = $input['Amount'][$key];
        $transaksis->Reference = $input['Reference'];
        $transaksis->POCode = $input['POCode'];
        $transaksis->save();
      }
      
      Invoice::where('invoice.Reference', $input['Reference'])->where('invoice.Periode', 1)->delete();
      
      $invoice = Invoice::select([
        DB::raw('MAX(id) AS maxid')
      ])
      ->first();
      $invoice2 = $invoice->maxid+1;

      $JSC = array_unique($JSC);

      $invoices = $JSC;
      foreach ($invoices as $key => $invoices)
      {
        $invoices = new Invoice;
        $invoices->id = $invoice2 + $key;
        $invoices->Invoice = str_pad($invoice2 + $key, 5, "0", STR_PAD_LEFT);
        $invoices->JSC = $JSC[$key];
        $invoices->Tgl = $request['Tgl'];
        $invoices->Reference = $request['Reference'];
        $invoices->Periode = 1; 
        $invoices->save();
      }
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Update PO on POCode '.$request['POCode'];
      $history->save();

    	return redirect()->route('po.show', $id);
    }

    public function destroy(Request $request, $id)
    {
      $po = PO::find($id);
      $transaksi = Transaksi::where('transaksi.POCode', $po->POCode);
      $transaksiid = $transaksi->pluck('id');
      $reference = Reference::where('pocustomer.Reference', $transaksi->first()->Reference)
      ->first();
      
      Transaksi::whereIn('id', $transaksiid)->delete();
      
      $invoice = Invoice::select('id')
      ->from('invoice as inv')
      ->whereNotExists(function($query)
        {
          $query->from('transaksi as tran')
          ->whereRaw('inv.Reference = tran.Reference AND inv.JSC = tran.JS');
        })
      ->whereRaw('(inv.JSC = "Sewa" OR inv.JSC = "Jual")')
      ->pluck('id');
      
      Invoice::whereIn('id', $invoice)->delete();
      
      PO::destroy($id);
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Delete PO on POCode '.$request['POCode'];
      $history->save();
      
      Session::flash('message', 'POCode '. $po->POCode .' deleted!');

    	return redirect()->route('reference.show', $reference->id);
    }
}
