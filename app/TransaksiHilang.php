<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaksiHilang extends Model
{
	protected $table = 'transaksihilang';
	protected $fillable =  ['id', 'Tgl', 'QHilang', 'Purchase', 'Periode', 'HilangText', 'SJ'];
	public $timestamps = false;
}
