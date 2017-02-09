<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	protected $table = 'invoice';
	protected $fillable =  ['id', 'Invoice', 'JSC', 'Tgl', 'Reference', 'Periode', 'PPN', 'Discount', 'Catatan', 'Lunas', 'Count', 
	'TglTerima', 'Termin', 'Times', 'TimesKembali', 'Pembulatan'];
	public $timestamps = false;
}