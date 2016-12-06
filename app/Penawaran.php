<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
	protected $table = 'penawaran';
	protected $fillable =  ['id', 'Penawaran', 'Tgl', 'Barang', 'Warehouse', 'JS', 'Quantity', 'Amount', 'PCode', 'ICode'];
	public $timestamps = false;
}