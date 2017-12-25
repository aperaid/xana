<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
	protected $table = 'inventory';
	protected $fillable =  ['id', 'Code', 'Barang', 'BeliPrice', 'JualPrice', 'Price', 'Type', 'Kumbang', 'BulakSereh', 'Legok', 'CitraGarden', 'Warehouse'];
	public $timestamps = false;
}
