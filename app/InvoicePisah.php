<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoicePisah extends Model
{
  protected $table = 'invoicepisah';
	protected $fillable =  ['id', 'Invoice', 'JSC', 'Tgl', 'Reference', 'Periode', 'PPN', 'Discount', 'Catatan', 'Lunas', 'Count', 'TglTerima', 'Termin', 'Times', 'TimesKembali', 'Pembulatan', 'POCode', 'Abjad'];
	public $timestamps = false;
}
