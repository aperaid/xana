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
        $transaksis->QSisaKem = $input['Quantity'][$key];
        $transaksis->Amount = $input['Amount'][$key];
        $transaksis->Reference = $input['Reference'];
        $transaksis->POCode = $input['POCode'];
        $transaksis->save();
      }
      
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
        $invoices->PPN = $request['PPN'];
        $invoices->Discount = str_replace(".","",substr($request->Discount, 3));
        $invoices->Catatan = $request['Catatan'];
        $invoices->save();
      }
      
      /*$x=0;
      foreach($jss as $js){
        $invoice[] = $maxid[$x];
        $invoice[] = str_pad($maxid[$x] + $x, 5, "0", STR_PAD_LEFT);
        $JSC = $js->JS;
        $Tgl = $request['Tgl'];
        $Reference = $request['Reference'];
        $Periode = 1;
        $PPN = $request['PPN'];
        $Discount = $request['Discount'];
        $Catatan = $request['Catatan'];
        $x++;
      }*/
      
      /*$invoice = Invoice::Create([
        'id' => $maxid,
        'Invoice' => $invoice,
        'JSC' => $JSC,
        'Tgl' => $Tgl,
        'Reference' => $Reference,
        'Periode' => $Periode,
        'PPN' => $PPN,
        'Discount' => $Discount,
        'Catatan' => $Catatan,
      ]);*/
      
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
      ->with('url', 'po')
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
