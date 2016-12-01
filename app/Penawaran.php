<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
	protected $table = 'penawaran';
	protected $fillable =  ['id', 'Penawaran', 'Tgl', 'Barang', 'Type', 'JS', 'Quantity', 'Amount', 'PCode'];
	public $timestamps = false;
}