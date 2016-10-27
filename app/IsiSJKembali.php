<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IsiSJKembali extends Model
{
	protected $table = 'isisjkembali';
	protected $fillable =  ['id', 'IsiSJKem', 'Warehouse', 'QTertanda', 'QTerima', 'Purchase', 'SJKem', 'Periode', 'IsiSJKir'];
	public $timestamps = false;
}