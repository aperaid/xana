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
use Session;
use DB;

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
      
      $purchase = Transaksi::orderby('id', 'desc')
      ->first();
      
      $js = Transaksi::distinct()
      ->select('transaksi.JS')
      ->where('transaksi.POCode', DB::raw("(select MAX(id) from transaksi where transaksi.Reference = '00001/080816')"));
      $invoice = Invoice::select([
        DB::raw('MAX(id) AS maxid')
      ]);

      return view('pages.po.create')
      ->with('po', $po)
      ->with('transaksi', $transaksi)
      ->with('id', $id)
      ->with('reference', $reference)
      ->with('purchase', $purchase)
      ->with('invoice', $invoice)
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
        'Transport' => $request['Transport'],
      ]);

      
      $transaksi = Transaksi::Create([
        'id' => $request['transaksiid'],
        'Purchase' => $request['Purchase'],
        'JS' => $request['JS'],
        'Barang' => $request['Barang'],
        'Quantity' => $request['Quantity'],
        'QSisaKirInsert' => $request['Quantity'],
        'QSisaKir' => $request['Quantity'],
        'QSisaKem' => $request['Quantity'],
        'Amount' => $request['Amount'],
        'Reference' => $request['Reference'],
        'POCode' => $request['POCode'],
      ]);
      
      $test = $request['Reference'];
      
      $jss = Transaksi::from('transaksi')->distinct()
      ->where('transaksi.POCode', DB::raw("(select MAX(id) from transaksi where transaksi.Reference = $test)"));
      $invoice = Invoice::select([
        DB::raw('MAX(id) AS maxid')
      ]);
      
      foreach($jss as $js){
      $invoice = Invoice::Create([
        'id' => 1,
        'Invoice' => 1,
        'JSC' => $js->JS,
        'Tgl' => $request['Tgl'],
        'Reference' => $request['Reference'],
        'Periode' => 1,
        'PPN' => $request['PPN'],
        'Discount' => $request['Discount'],
        'Catatan' => $request['Catatan'],
      ]);
      }
      
    	return redirect()->route('reference.show', $id);
    }

    public function show($POCode)
    {
      $po = PO::find($POCode);
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
      ->with('po', $po)
      ->with('id', $id)
      ->with('transaksis', $transaksi)
      ->with('pocheck', $pocheck)
      ->with('poexist', $poexist)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Show');
    }

    public function edit($POCode)
    {
    	$po = PO::find($POCode);
      $transaksi = Transaksi::where('transaksi.POCode', $po -> POCode)
      ->get();
      
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
      ->with('po', $po)
      ->with('transaksis', $transaksi)
      ->with('last_purchase', $last_purchase)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $POCode)
    {
    	$po = PO::find($POCode);
      $transaksi = Transaksi::find($POCode);

    	$po->id = $request->id;
    	$po->POCode = $request->POCode;
      $po->Tgl = $request->Tgl;
    	$po->Catatan = $request->Catatan;
      $po->Transport = $request->Transport;
    	$po->save();
      
      $transaksi->id = $request->id;
    	$transaksi->Purchase = $request->Purchase;
      $transaksi->JS = $request->JS;
      $transaksi->Barang = $request->Barang;
    	$transaksi->Quantity = $request->Quantity;
      $transaksi->QSisaKirInsert = $request->QSisaKirInsert;
      $transaksi->QSisaKir = $request->QSisaKir;
    	$transaksi->QSisaKem = $request->QSisaKem;
      $transaksi->Amount = $request->Amount;
    	$transaksi->Reference = $request->Reference;
      $transaksi->POCode = $request->POCode;
    	$transaksi->save();

    	return redirect()->route('po.show', $POCode);
    }

    public function destroy($POCode)
    {
    	PO::destroy($POCode);
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('reference.index');
    }
}
