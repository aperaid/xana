<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SJKirim;
use App\Invoice;
use App\InvoicePisah;
use App\Periode;
use App\Inventory;
use Session;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
			$sjkirims = SJKirim::leftJoin('isisjkirim', 'sjkirim.SJKir', '=', 'isisjkirim.SJKir')
			->where('QTertanda', 0)
			->groupBy('sjkirim.id')->get();
			
			$inventories = Inventory::where('Kumbang', '<', '10')
			->orWhere('BulakSereh', '<', '10')
			->orWhere('Legok', '<', '10')
			->orWhere('CitraGarden', '<', '10')
			->get();
			
			$periodes = Periode::whereRaw('DATE(DATE_FORMAT(STR_TO_DATE(E, "%d/%m/%Y"), "%Y-%m-%d")) <= DATE_ADD(CURDATE(), INTERVAL +1 DAY)')
			->whereRaw('Periode IN (select max(Periode) from Periode group by Reference)')
			->groupBy('Reference')
			->get();
			
			$invoices = Invoice::leftJoin('pocustomer', 'invoice.Reference', '=', 'pocustomer.Reference')
			->whereRaw('DATE(DATE_ADD(DATE_FORMAT(STR_TO_DATE(TglTerima, "%d/%m/%Y"), "%Y-%m-%d"), INTERVAL + Termin DAY)) <= DATE_ADD(CURDATE(), INTERVAL +1 DAY)')
			->groupBy('invoice.Reference', 'Periode')
			->where('INVP', 0)
			->get();
			
			$invoicepisahs = InvoicePisah::leftJoin('pocustomer', 'invoicepisah.Reference', '=', 'pocustomer.Reference')
			->whereRaw('DATE(DATE_ADD(DATE_FORMAT(STR_TO_DATE(TglTerima, "%d/%m/%Y"), "%Y-%m-%d"), INTERVAL + Termin DAY)) <= DATE_ADD(CURDATE(), INTERVAL +1 DAY)')
			->groupBy('invoicepisah.Reference', 'Periode')
			->where('INVP', 1)
			->get();
			
      return view('home')
			->with('sjkirims', $sjkirims)
			->with('inventories', $inventories)
			->with('periodes', $periodes)
			->with('invoices', $invoices)
			->with('invoicepisahs', $invoicepisahs)
      ->with('url', 'home')
      ->with('top_menu_sel', 'menu_home')
  		->with('page_title', 'Dashboard')
  		->with('page_description', 'Home');
    }
}
