<?php

use Illuminate\Database\Seeder;
use App\Customer;
use App\Project;
use App\Reference;

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
  }
}
