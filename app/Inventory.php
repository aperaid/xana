<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
	protected $table = 'inventory';
	protected $fillable =  ['id', 'Code', 'Barang', 'JualPrice', 'Price', 'Type', 'Kumbang', 'BulakSereh', 'Legok', 'CitraGarden'];
	public $timestamps = false;
}
