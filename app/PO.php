<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PO extends Model
{
	protected $table = 'po';
	protected $fillable =  ['id', 'POCode', 'Tgl', 'Catatan', 'Transport'];
	public $timestamps = false;
}

class Transaksi extends Model
{
	protected $table = 'transaksi';
	protected $fillable =  ['id', 'Purchase', 'JS', 'Barang', 'Quantity', 'QSisaKirInsert', 'QSisaKir', 'QSisaKem', 'Amount', 'Reference', 'POCode'];
	public $timestamps = false;
}