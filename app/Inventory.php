<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
	protected $table = 'inventory';
	protected $fillable =  ['id', 'Code', 'Barang', 'Price', 'Jumlah', 'Type', 'Warehouse'];
	public $timestamps = false;
}
