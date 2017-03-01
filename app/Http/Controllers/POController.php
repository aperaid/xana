<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Customer;
use App\Project;
use App\PO;
use App\Transaksi;
use App\Reference;
use App\Invoice;
use App\InvoicePisah;
use App\History;
use App\Penawaran;
use App\Periode;
use App\Inventory;
use Session;
use DB;
use Auth;

class POController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Admin'||Auth::user()->access=='CUSTINVPPN'||Auth::user()->access=='CUSTINVNONPPN'))
				$this->access = array("create", "create2", "create3", "show", "edit");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
  public function create()
  {
    $last_transaksi = Transaksi::max('id');
    
    if($last_transaksi == 0){
      $maxid = 0;
    }else{
      $maxid = $last_transaksi;
    }
    
    $reference = Reference::find(Input::get('id'));
  
    $last_po = PO::max('id');
    
		$maxperiode = Periode::where('reference', $reference->Reference)
		->max('Periode');
		if($maxperiode==0 || $maxperiode==1){
			$min = $reference->Tgl;
		}else{
			$periode = Periode::where('reference', $reference->Reference)
			->where('Periode', $maxperiode)
			->first();
			$min = $periode->S;
		}

    if(in_array("create", $this->access)){
      return view('pages.po.create')
      ->with('url', 'po')
      ->with('last_po', $last_po)
      ->with('maxid', $maxid)
      ->with('reference', $reference)
			->with('min', $min)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Item');
    }else
        return redirect()->back();
  }
  
  public function getCreate2($id)
  {
		if(in_array("create2", $this->access)){
			return view('pages.po.create2')
			->with('url', 'po')
			->with('id', $id)
			->with('top_menu_sel', 'menu_referensi')
			->with('page_title', 'Purchase Order')
			->with('page_description', 'Penawaran');
		}else
			return redirect()->back();
  }
  
  public function getCreate3(Request $request, $id)
  {
    $penawaran = $request->Penawaran;
    
    $last_transaksi = Transaksi::max('id');
    
    if($last_transaksi == 0){
      $maxid = 0;
    }else{
      $maxid = $last_transaksi;
    }
    
    $penawarans = Penawaran::select('penawaran.*', 'inventory.Type')
    ->leftJoin('inventory', 'penawaran.ICode', '=', 'inventory.Code')
    ->where('penawaran.Penawaran', $penawaran)
    ->orderBy('penawaran.id', 'asc')
    ->get();
    
    $po = PO::select([
      DB::raw('MAX(po.id) AS maxid')
    ])
    ->first();
    
    $reference = Reference::where('pocustomer.id', $id)
    ->first();

    if(in_array("create3", $this->access)){
      return view('pages.po.create3')
      ->with('url', 'po')
      ->with('maxid', $maxid)
      ->with('id', $id)
      ->with('penawarans', $penawarans)
      ->with('po', $po)
      ->with('reference', $reference)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Item');
    }else
      return redirect()->back();
  }

  public function store(Request $request)
  {
    $reference = Reference::find(Input::get('id'));
		$forgettransaksi = $request->Purchase[0];
		if($forgettransaksi==null){
			return redirect()->route('po.create', 'id=' .$reference->id)->with('error', 'Please add item first!');
		}else{
			//get last periode if exist
			$maxperiode = Periode::where('Reference', $request->Reference)
			->max('Periode');
			if(isset($maxperiode))
				$periode = $maxperiode;
			else
				$periode = 1;
			
			//get first count for periode that start from 1 after new year if exist
			$firstcount = Invoice::where('Reference', $request->Reference)
			->where('Periode', $maxperiode)
			->first();
			if(isset($firstcount))
				$count = $firstcount->Count;
			else
				$count = 1;
			
			//get termin if exist
			$firsttermin = Invoice::where('Reference', $request->Reference)
			->first();
			if(isset($firsttermin))
				$termin = $firsttermin->Termin;
			else
				$termin = 0;
			
			$is_exist = PO::where('POCode', $request->POCode)->first();
			if(isset($is_exist->POCode)){
				return redirect()->route('po.create', 'id=' .$reference->id)->with('error', 'Reference with POCode '.strtoupper($request->POCode).' is already exist!');
			}else{
				$po = PO::Create([
					'id' => $request['poid'],
					'POCode' => $request['POCode'],
					'Tgl' => $request['Tgl'],
					'Periode' => $periode,
					'Catatan' => $request['Catatan'],
				]);
			}

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
				$transaksis->Amount = str_replace(".","",substr($input['Amount'][$key], 3));
				$transaksis->Reference = $input['Reference'];
				$transaksis->POCode = $input['POCode'];
				$transaksis->ICode = $input['ICode'][$key];
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
			
			$projectcode = Reference::where('Reference', $request['Reference'])->first()->PCode;
			$PPN = Customer::leftJoin('project', 'customer.CCode', '=', 'project.CCode')
			->where('PCode', $projectcode)
			->first()->PPN;
			
			$JSC = array_unique($JSC);
			
			$abjad = InvoicePisah::where('Reference', $reference->Reference)->max('Abjad');
			if($abjad==0)
				$x = 1;
			else
				$x = $abjad+1;
			if($x==1)$y='';else if($x==2)$y='A';else if($x==3)$y='B';else if($x==4)$y='C';else if($x==5)$y='D';else if($x==6)$y='E';else if($x==7)$y='F';else if($x==8)$y='G';else if($x==9)$y='H';else if($x==10)$y='I';else if($x==11)$y='J';else if($x==12)$y='K';else if($x==13)$y='L';else if($x==14)$y='M';else if($x==15)$y='N';else if($x==16)$y='O';else if($x==17)$y='P';else if($x==18)$y='Q';else if($x==19)$y='R';else if($x==20)$y='S';else if($x==21)$y='T';else if($x==22)$y='U';else if($x==23)$y='V';else if($x==24)$y='W';else if($x==25)$y='X';else if($x==26)$y='Y';else if($x==27)$y='Z';
			$invoices = $JSC;
			foreach ($invoices as $key => $invoices)
			{
				$last_invoice = Invoice::max('id')+1;
				
				$invoices = new Invoice;//Invoice::updateOrCreate(['Reference' => $request['Reference'], 'JSC' => $JSC[$key]]);
				$invoices->id = $last_invoice;
				if($JSC[$key]=="Sewa"){
					$invoices->Invoice = $projectcode."/".$count."/".substr($request['Tgl'], 3, -5).substr($request['Tgl'], 6)."/BDN";
				}else{
					$invoices->Invoice = $projectcode."/".substr($request['Tgl'], 3, -5)."/".substr($request['Tgl'], 6);
				}
				$invoices->JSC = $JSC[$key];
				$invoices->Tgl = $request['Tgl'];
				$invoices->Reference = $request['Reference'];
				$invoices->Periode = $periode;
				$invoices->PPN = $PPN;
				$invoices->Count = $count;
				$invoices->Termin = $termin;
				$invoices->save();

				$last_invoicepisah = InvoicePisah::max('id')+1;
				$invoicepisah = new InvoicePisah;
				$invoicepisah->id = $last_invoicepisah;
				if($JSC[$key]=="Sewa"){
					$invoicepisah->Invoice = $projectcode.$y."/".$count."/".substr($request['Tgl'], 3, -5).substr($request['Tgl'], 6)."/BDN";
				}else{
					$invoicepisah->Invoice = $projectcode.$y."/".substr($request['Tgl'], 3, -5)."/".substr($request['Tgl'], 6);
				}
				$invoicepisah->JSC = $JSC[$key];
				$invoicepisah->Tgl = $request['Tgl'];
				$invoicepisah->Reference = $request['Reference'];
				$invoicepisah->Periode = $periode;
				$invoicepisah->PPN = $PPN;
				$invoicepisah->Count = $count;
				$invoicepisah->Termin = $termin;
				$invoicepisah->POCode = $request['POCode'];
				$invoicepisah->Abjad = $x;
				$invoicepisah->save();
				
				$duplicateRecords = Invoice::select([
					DB::raw('MAX(id) AS maxid')
				])
				->selectRaw('count(`Reference`) as `occurences`')
				->where('Reference', $input['Reference'])
				->groupBy('JSC', 'Periode')
				->having('occurences', '>', 1)
				->pluck('maxid');
				
				/*$duplicateRecords2 = Invoice::select([
					DB::raw('MAX(id) AS maxid')
				])
				->selectRaw('count(`Reference`) as `occurences`')
				->where('Reference', $input['Reference'])
				->where('JSC', 'Jual')
				->groupBy('JSC')
				->having('occurences', '>', 1)
				->pluck('maxid');*/
				
				Invoice::whereIn('id', $duplicateRecords)->delete();
				//Invoice::whereIn('id', $duplicateRecords2)->delete();
				DB::statement('ALTER TABLE invoice auto_increment = 1;');
			}
			
			$history = new History;
			$history->User = Auth::user()->name;
			$history->History = 'Create PO on POCode '.$request['POCode'];
			$history->save();
			
			return redirect()->route('reference.show', $reference->id);
		}
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
    
    if(in_array("show", $this->access)){
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
    }else
      return redirect()->back();
  }

  public function edit($id)
  {
    $po = PO::find($id);
		
		$transaksi = Transaksi::where('POCode', $po->POCode)->first();
		
		$maxperiode = Periode::where('reference', $transaksi->Reference)
		->max('Periode');
		if($maxperiode==0 || $maxperiode==1){
			$min = $po->Tgl;
		}else{
			$periode = Periode::where('reference', $transaksi->Reference)
			->where('Periode', $maxperiode)
			->first();
			$min = $periode->S;
		}
		
    $transaksis = Transaksi::select('transaksi.*', 'inventory.Type')
    ->leftJoin('inventory', 'transaksi.ICode', '=', 'inventory.Code')
    ->where('transaksi.POCode', $po -> POCode)
    ->get();
    $last_transaksi = Transaksi::max('id');
    
    $poitem = Transaksi::where('transaksi.POCode', $po -> POCode)
    ->first();
    $maxpurchase = Transaksi::select([
      DB::raw('MAX(Purchase) AS maxpurchase')
    ])
    ->first();
    $minpurchase = Transaksi::select([
      DB::raw('MIN(Purchase) AS minpurchase')
    ])
    ->groupBy('POCode')
    ->orderBy('id', 'asc')
    ->first();
    if ($poitem['Id']==$minpurchase['minpurchase'])
    {
      $last_purchase = $minpurchase['minpurchase']-1;
    } else
    {
      $last_purchase = $maxpurchase['maxpurchase'];
    }
    
    $invoice = Invoice::where('Reference', $transaksis->first()->Reference)->where('periode', 1)
    ->first();

    if(in_array("edit", $this->access)){
      return view('pages.po.edit')
      ->with('url', 'po')
      ->with('po', $po)
      ->with('last_transaksi', $last_transaksi)
      ->with('transaksis', $transaksis)
      ->with('last_purchase', $last_purchase)
      ->with('invoice', $invoice)
			->with('min', $min)
      ->with('top_menu_sel', 'menu_referensi')
      ->with('page_title', 'Purchase Order')
      ->with('page_description', 'Edit');
    }else
      return redirect()->back();
  }

  public function update(Request $request, $id)
  {
    $po = PO::find($id);
    $transaksi = Transaksi::where('transaksi.POCode', $request -> POCode);
		
		$periode = InvoicePisah::where('POCode', $request->POCode)
		->first()->Periode;
		
		$count = InvoicePisah::where('POCode', $request->POCode)
		->first()->Count;
		
		//get termin if exist
		$firsttermin = Invoice::where('Reference', $request->Reference)
		->first();
		if(isset($firsttermin))
			$termin = $firsttermin->Termin;
		else
			$termin = 0;

    $po->id = $request->poid;
    $po->Tgl = $request->Tgl;
    $po->Catatan = $request->Catatan;
    $po->save();

    $ids = $transaksi->pluck('id');
    Transaksi::whereIn('id', $ids)->delete();
    DB::statement('ALTER TABLE transaksi auto_increment = 1;');
    
    $input = Input::all();
    $transaksis = $input['transaksiid'];
    foreach ($transaksis as $key => $transaksis)
    {
      $transaksis = new Transaksi;
      $transaksis->id = $input['transaksiid'][$key];
      $transaksis->Purchase = $input['Purchase'][$key];
      $transaksis->JS = $input['JS'][$key];
			$JSCBaru[] = $input['JS'][$key];
      $transaksis->Barang = $input['Barang'][$key];
      $transaksis->Quantity = $input['Quantity'][$key];
      $transaksis->QSisaKirInsert = $input['Quantity'][$key];
      $transaksis->QSisaKir = $input['Quantity'][$key];
      $transaksis->Amount = str_replace(".","",substr($input['Amount'][$key], 3));
      $transaksis->Reference = $input['Reference'];
      $transaksis->POCode = $input['POCode'];
      $transaksis->ICode = $input['ICode'][$key];
      $transaksis->save();
    }
    
    //Invoice::where('invoice.Reference', $input['Reference'])->where('invoice.Periode', 1)->delete();

    $projectcode = Reference::where('Reference', $request['Reference'])->first()->PCode;
		$PPN = Customer::leftJoin('project', 'customer.CCode', '=', 'project.CCode')
		->where('PCode', $projectcode)
		->first()->PPN;
    
    $JSCLama = Transaksi::where('reference', $request['Reference'])->pluck('JS')->toArray();
		$combineJSC = array_merge($JSCLama, $JSCBaru);
    $JSC = array_unique($combineJSC);
    
    /*Invoice::where('Reference', $request['Reference'])
		->where('Periode', 1)
		->where(function($query){
			$query->where('JSC', "Sewa")
			->orWhere('JSC', "Jual");
		})
		->delete();
    DB::statement('ALTER TABLE invoice auto_increment = 1;');*/

    $invoices = $JSC;
    foreach ($invoices as $key => $invoices)
    {
      $last_invoice = Invoice::max('id')+1;
      
      $invoices = new Invoice;
      $invoices->id = $last_invoice;
      if($JSC[$key]=="Sewa"){
        $invoices->Invoice = $projectcode."/".$count."/".substr($request['Tgl'], 3, -5).substr($request['Tgl'], 6)."/BDN";
      }else{
        $invoices->Invoice = $projectcode."/".substr($request['Tgl'], 3, -5)."/".substr($request['Tgl'], 6);
      }
      $invoices->JSC = $JSC[$key];
      $invoices->Tgl = $request['Tgl'];
      $invoices->Reference = $request['Reference'];
      $invoices->Periode = $periode;
			$invoices->PPN = $PPN;
      $invoices->Count = $count;
			$invoices->Termin = $termin;
      $invoices->save();
      
      $duplicateRecords = Invoice::select([
        DB::raw('MAX(id) AS maxid')
      ])
      ->selectRaw('count(`Reference`) as `occurences`')
      ->where('Reference', $input['Reference'])
      ->groupBy('JSC', 'Periode')
      ->having('occurences', '>', 1)
      ->pluck('maxid');
			
			/*$duplicateRecords2 = Invoice::select([
        DB::raw('MAX(id) AS maxid')
      ])
      ->selectRaw('count(`Reference`) as `occurences`')
      ->where('Reference', $input['Reference'])
			->where('JSC', 'Jual')
      ->groupBy('JSC')
      ->having('occurences', '>', 1)
      ->pluck('maxid');*/
			
			$duplicateRecords3 = Invoice::select([
        DB::raw('MAX(id) AS maxid')
      ])
      ->selectRaw('count(`Reference`) as `occurences`')
      ->where('Reference', $input['Reference'])
			->whereNotIn('JSC', $JSC)
      ->pluck('maxid');
      
      Invoice::whereIn('id', $duplicateRecords)->delete();
			//Invoice::whereIn('id', $duplicateRecords2)->delete();
			Invoice::whereIn('id', $duplicateRecords3)->delete();
      DB::statement('ALTER TABLE invoice auto_increment = 1;');
    }
		
		/*InvoicePisah::where('POCode', $request['POCode'])
		->where(function($query){
			$query->where('JSC', "Sewa")
			->orWhere('JSC', "Jual");
		})
		->delete();
    DB::statement('ALTER TABLE invoicepisah auto_increment = 1;');*/
		
		$JSCLamaPisah = Transaksi::where('reference', $request['Reference'])->where('POCode', $request['POCode'])->pluck('JS')->toArray();
		$combineJSC = array_merge($JSCLamaPisah, $JSCBaru);
		$JSCPisah = array_unique($combineJSC);
		
		$abjad = InvoicePisah::where('POCode', $request->POCode)->first()->Abjad;
		$x = $abjad;
					
		if($x==1)$y='';else if($x==2)$y='A';else if($x==3)$y='B';else if($x==4)$y='C';else if($x==5)$y='D';else if($x==6)$y='E';else if($x==7)$y='F';else if($x==8)$y='G';else if($x==9)$y='H';else if($x==10)$y='I';else if($x==11)$y='J';else if($x==12)$y='K';else if($x==13)$y='L';else if($x==14)$y='M';else if($x==15)$y='N';else if($x==16)$y='O';else if($x==17)$y='P';else if($x==18)$y='Q';else if($x==19)$y='R';else if($x==20)$y='S';else if($x==21)$y='T';else if($x==22)$y='U';else if($x==23)$y='V';else if($x==24)$y='W';else if($x==25)$y='X';else if($x==26)$y='Y';else if($x==27)$y='Z';
		$invoicepisahs = $JSCPisah;
    foreach ($invoicepisahs as $key => $invoices)
    {
			$last_invoicepisah = InvoicePisah::max('id')+1;

			$invoicepisah = new InvoicePisah;
      $invoicepisah->id = $last_invoicepisah;
      if($JSCPisah[$key]=="Sewa"){
        $invoicepisah->Invoice = $projectcode.$y."/".$count."/".substr($request['Tgl'], 3, -5).substr($request['Tgl'], 6)."/BDN";
      }else{
        $invoicepisah->Invoice = $projectcode.$y."/".substr($request['Tgl'], 3, -5)."/".substr($request['Tgl'], 6);
      }
      $invoicepisah->JSC = $JSCPisah[$key];
      $invoicepisah->Tgl = $request['Tgl'];
      $invoicepisah->Reference = $request['Reference'];
      $invoicepisah->Periode = $periode;
			$invoicepisah->PPN = $PPN;
      $invoicepisah->Count = $count;
			$invoicepisah->Termin = $termin;
			$invoicepisah->POCode = $request['POCode'];
			$invoicepisah->Abjad = $x;
      $invoicepisah->save();
			
			$duplicateRecords = InvoicePisah::select([
        DB::raw('MAX(id) AS maxid')
      ])
      ->selectRaw('count(`Reference`) as `occurences`')
      ->where('Reference', $input['Reference'])
      ->groupBy('JSC', 'Periode', 'Abjad')
      ->having('occurences', '>', 1)
      ->pluck('maxid');
			
			$duplicateRecords3 = InvoicePisah::select([
        DB::raw('MAX(id) AS maxid')
      ])
      ->selectRaw('count(`Reference`) as `occurences`')
      ->where('POCode', $input['POCode'])
			->whereNotIn('JSC', $JSCBaru)
      ->pluck('maxid');
			
			InvoicePisah::whereIn('id', $duplicateRecords)->delete();
			InvoicePisah::whereIn('id', $duplicateRecords3)->delete();
      DB::statement('ALTER TABLE invoicepisah auto_increment = 1;');
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
    DB::statement('ALTER TABLE transaksi auto_increment = 1;');
    
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
    DB::statement('ALTER TABLE invoice auto_increment = 1;');
		
		InvoicePisah::where('POCode', $po->POCode)->delete();
		DB::statement('ALTER TABLE invoicepisah auto_increment = 1;');
    
    PO::destroy($id);
    DB::statement('ALTER TABLE po auto_increment = 1;');

    $history = new History;
    $history->User = Auth::user()->name;
    $history->History = 'Delete PO on POCode '.$request['POCode'];
    $history->save();
    
    Session::flash('message', 'POCode '. $po->POCode .' deleted!');

    return redirect()->route('reference.show', $reference->id);
  }
}
