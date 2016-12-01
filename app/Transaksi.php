<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
	protected $table = 'transaksi';
	protected $fillable =  ['id', 'Purchase', 'Type', 'JS', 'Barang', 'Quantity', 'QSisaKirInsert', 'QSisaKir', 'QSisaKem', 'Amount', 'Reference', 'POCode'];
	public $timestamps = false;
}