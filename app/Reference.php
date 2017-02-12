<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
	protected $table = 'pocustomer';
	protected $fillable =  ['id', 'Reference', 'Tgl', 'PCode', 'Transport', 'PPN', 'PPNT', 'INVP'];
	public $timestamps = false;
}
