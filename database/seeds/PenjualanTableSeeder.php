<?php

use Illuminate\Database\Seeder;
use App\Customer;
use App\Project;
use App\Reference;
use App\PO;
use App\Transaksi;
use App\SJKirim;
use App\IsiSJKirim;
use App\Periode;
use App\Invoice;
use App\TransaksiClaim;

class PenjualanTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Customer::truncate();
    Project::truncate();
    Reference::truncate();
		PO::truncate();
		Transaksi::truncate();
		SJKirim::truncate();
		IsiSJKirim::truncate();
		Periode::truncate();
		Invoice::truncate();
		TransaksiClaim::truncate();
    Customer::insert([
      ['id'=>'1','CCode'=>'BCA','PPN'=>'1','Company'=>'PT. BANK CENTRAL ASIA','Customer'=>'Thomas Chandra','CompAlamat' => 'Jl. M.H Thamrin No.1', 'CompZip'=>'12260', 'CompKota'=>'Jakarta', 'CompPhone'=>'021-123456', 'CompEmail'=>'bankbca@bca.co.id', 'CustPhone'=>'021-123456', 'CustEmail'=>'customer@bca.co.id', 'Fax'=>'021-123456', 'NPWP'=>'1234567890'],
      ['id'=>'2','CCode'=>'MAND','PPN'=>'0','Company'=>'PT. BANK MANDIRI INDONESIA','Customer'=>'Tommy Sugiarto','CompAlamat' => 'Jl. Kuningan Mulia No. 1', 'CompZip'=>'12262', 'CompKota'=>'Jakarta', 'CompPhone'=>'021-123456', 'CompEmail'=>'bankmandiri@mandiri.co.id', 'CustPhone'=>'021-123456', 'CustEmail'=>'customer@mandiri.co.id', 'Fax'=>'021-123456', 'NPWP'=>'1234567890']
    ]);
    Project::insert([
      ['id'=>'1','PCode'=>'BCA01','Project'=>'BCA PROJECT','ProjAlamat'=>'Jln M.H. Thamrin No.1','ProjZip' => '10310', 'ProjKota'=>'Jakarta', 'CCode'=>'BCA'],
      ['id'=>'2','PCode'=>'MAN01','Project'=>'MANDIRI PROJECT','ProjAlamat'=>'Jl. Mega Kuningan No. 1','ProjZip' => '12190', 'ProjKota'=>'Jakarta', 'CCode'=>'MAND']
    ]);
    Reference::insert([
      ['id'=>'1','Reference'=>'00001/260117','Tgl'=>'27/01/2017','PCode'=>'BCA01','Transport' => '300000', 'PPNT'=>'1', 'INVP'=>'0']
    ]);
		PO::insert([
      ['id'=>'1','POCode'=>'BCA01/00001','Tgl'=>'01/03/2017','Periode'=>'1','Catatan' => 'catatan']
    ]);
		Transaksi::insert([
      ['id'=>'1','Purchase'=>'1','JS'=>'Sewa','Barang'=>'MAIN FRAME 150','Quantity' => '10','QSisaKirInsert' => '0','QSisaKir' => '0','QSisaKem' => '10','Amount' => '8000','Reference' => '00001/260117','POCode' => 'BCA01/00001','ICode' => 'MF150L'],
			['id'=>'2','Purchase'=>'2','JS'=>'Sewa','Barang'=>'MAIN FRAME 170','Quantity' => '10','QSisaKirInsert' => '0','QSisaKir' => '0','QSisaKem' => '10','Amount' => '9000','Reference' => '00001/260117','POCode' => 'BCA01/00001','ICode' => 'MF170L']
    ]);
		SJKirim::insert([
      ['id'=>'1','SJKir'=>'001/SI/032017','Tgl'=>'01/03/2017','Reference'=>'00001/260117']
    ]);
		IsiSJKirim::insert([
      ['id'=>'1','IsiSJKir'=>'1','QKirim'=>'10','QTertanda'=>'10','QSisaKemInsert' => '10','QSisaKem' => '10','Warehouse' => 'Kumbang','Purchase' => '1','SJKir' => '001/SI/032017'],
			['id'=>'2','IsiSJKir'=>'2','QKirim'=>'10','QTertanda'=>'10','QSisaKemInsert' => '10','QSisaKem' => '10','Warehouse' => 'Kumbang','Purchase' => '2','SJKir' => '001/SI/032017']
    ]);
		Periode::insert([
      ['id'=>'1','Periode'=>'1','S'=>'01/03/2017','E'=>'31/03/2017','Quantity' => '10','IsiSJKir' => '1','Reference' => '00001/260117','Purchase' => '1', 'Deletes' => 'Sewa'],
			['id'=>'2','Periode'=>'1','S'=>'01/03/2017','E'=>'31/03/2017','Quantity' => '10','IsiSJKir' => '2','Reference' => '00001/260117','Purchase' => '2', 'Deletes' => 'Sewa']
    ]);
		Invoice::insert([
      ['id'=>'1','Invoice'=>'BCA01/1/032017/BDN','JSC'=>'Sewa','Tgl'=>'01/03/2017','Reference' => '00001/260117','Periode' => '1','PPN' => '1','Discount' => '0', 'Lunas' => '0', 'Count' => '1', 'Termin' => '0', 'Times' => '1', 'TimesKembali' => '0', 'Pembulatan' => '0']
    ]);
  }
}
