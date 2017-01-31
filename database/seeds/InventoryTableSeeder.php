<?php

use Illuminate\Database\Seeder;
use App\Inventory;

class InventoryTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Inventory::truncate();
    Inventory::insert([
      ['Code'=>'MF150B','Barang'=>'MAIN FRAME 150','JualPrice'=>'900000','Price'=>'9000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'MF150L','Barang'=>'MAIN FRAME 150','JualPrice'=>'800000','Price'=>'8000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'MF170B','Barang'=>'MAIN FRAME 170','JualPrice'=>'1000000','Price'=>'10000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'MF170L','Barang'=>'MAIN FRAME 170','JualPrice'=>'900000','Price'=>'9000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'MF190B','Barang'=>'MAIN FRAME 190','JualPrice'=>'1100000','Price'=>'11000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'MF190L','Barang'=>'MAIN FRAME 190','JualPrice'=>'1000000','Price'=>'10000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'LF050B','Barang'=>'LADDER FRAME 050','JualPrice'=>'600000','Price'=>'6000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'LF050L','Barang'=>'LADDER FRAME 050','JualPrice'=>'500000','Price'=>'5000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'LF090B','Barang'=>'LADDER FRAME 090','JualPrice'=>'600000','Price'=>'6000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'LF090L','Barang'=>'LADDER FRAME 090','JualPrice'=>'500000','Price'=>'5000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'HF105B','Barang'=>'HORIZONTAL FRAME 105','JualPrice'=>'650000','Price'=>'6500' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'HF105L','Barang'=>'HORIZONTAL FRAME 105','JualPrice'=>'550000','Price'=>'5500' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'CB193B','Barang'=>'CROSS BRACE 193','JualPrice'=>'750000','Price'=>'7500' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'CB193L','Barang'=>'CROSS BRACE 193','JualPrice'=>'650000','Price'=>'6500' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'CB220B','Barang'=>'CROSS BRACE 220','JualPrice'=>'750000','Price'=>'7500' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'CB220L','Barang'=>'CROSS BRACE 220','JualPrice'=>'650000','Price'=>'6500' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'JB040B','Barang'=>'JACK BASE 040','JualPrice'=>'600000','Price'=>'6000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'JB040L','Barang'=>'JACK BASE 040','JualPrice'=>'500000','Price'=>'5000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'JB060B','Barang'=>'JACK BASE 060','JualPrice'=>'650000','Price'=>'6500' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'JB060L','Barang'=>'JACK BASE 060','JualPrice'=>'550000','Price'=>'5500' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'UH040B','Barang'=>'U HEAD 040','JualPrice'=>'600000','Price'=>'6000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'UH040L','Barang'=>'U HEAD 040','JualPrice'=>'500000','Price'=>'5000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'UH060B','Barang'=>'U HEAD 060','JualPrice'=>'650000','Price'=>'6500' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'UH060L','Barang'=>'U HEAD 060','JualPrice'=>'550000','Price'=>'5500' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'JPTN1B','Barang'=>'JOIN PIN TN1','JualPrice'=>'400000','Price'=>'4000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'JPTN1L','Barang'=>'JOIN PIN TN1','JualPrice'=>'300000','Price'=>'3000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'CPFB','Barang'=>'CLAMP PIPA FLEX/FIX','JualPrice'=>'400000','Price'=>'4000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'CPFL','Barang'=>'CLAMP PIPA FLEX/FIX','JualPrice'=>'300000','Price'=>'3000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'SF170B','Barang'=>'SIMPLE FRAME 170','JualPrice'=>'850000','Price'=>'8500' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'SF170L','Barang'=>'SIMPLE FRAME 170','JualPrice'=>'750000','Price'=>'7500' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'CW220B','Barang'=>'CAT WALK 220','JualPrice'=>'3500000','Price'=>'35000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'CW220L','Barang'=>'CAT WALK 220','JualPrice'=>'3400000','Price'=>'34000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'CW170B','Barang'=>'CAT WALK 170','JualPrice'=>'2000000','Price'=>'20000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'CW170L','Barang'=>'CAT WALK 170','JualPrice'=>'1900000','Price'=>'19000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'PSM90B','Barang'=>'PIPE SUPPORT M90','JualPrice'=>'2600000','Price'=>'26000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'PSM90L','Barang'=>'PIPE SUPPORT M90','JualPrice'=>'2500000','Price'=>'25000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'PG4MB','Barang'=>'PIPA GALVANIS 4M','JualPrice'=>'2700000','Price'=>'27000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'PG4ML','Barang'=>'PIPA GALVANIS 4M','JualPrice'=>'2600000','Price'=>'26000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'PG3MB','Barang'=>'PIPA GALVANIS 3M','JualPrice'=>'2500000','Price'=>'25000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'PG3ML','Barang'=>'PIPA GALVANIS 3M','JualPrice'=>'2400000','Price'=>'24000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'PG6MB','Barang'=>'PIPA GALVANIS 6M','JualPrice'=>'3000000','Price'=>'30000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'PG6ML','Barang'=>'PIPA GALVANIS 6M','JualPrice'=>'2900000','Price'=>'29000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'T170B','Barang'=>'TANGGA 170','JualPrice'=>'6000000','Price'=>'60000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'T170L','Barang'=>'TANGGA 170','JualPrice'=>'5900000','Price'=>'59000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'T190B','Barang'=>'TANGGA 190','JualPrice'=>'6500000','Price'=>'65000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'T190L','Barang'=>'TANGGA 190','JualPrice'=>'6400000','Price'=>'64000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
      ['Code'=>'RC4B','Barang'=>'RODA/CASTER 4 BUAH','JualPrice'=>'15000000','Price'=>'150000' ,'Type'=>'NEW','Kumbang'=>'110', 'BulakSereh'=>'120', 'Legok'=>'130', 'CitraGarden'=>'140'],
      ['Code'=>'RC4L','Barang'=>'RODA/CASTER 4 BUAH','JualPrice'=>'14000000','Price'=>'140000' ,'Type'=>'SECOND','Kumbang'=>'115', 'BulakSereh'=>'125', 'Legok'=>'135', 'CitraGarden'=>'145'],
    ]);
  }
}
