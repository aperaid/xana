<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceKirim extends Model
{
  protected $table = 'invoicekirim';
	protected $fillable =  ['id', 'Invoice', 'JSC', 'Tgl', 'Reference', 'Periode', 'PPN', 'Discount', 'Catatan', 'Lunas', 'Count', 'TglTerima', 'Termin', 'Times', 'TimesKembali', 'Pembulatan', 'SJKirim', 'Abjad'];
	public $timestamps = false;
}
