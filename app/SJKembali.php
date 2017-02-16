<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SJKembali extends Model
{
	protected $table = 'sjkembali';
	protected $fillable =  ['id', 'SJKem', 'Tgl', 'Reference', 'NoPolisi', 'Sopir', 'Kenek'];
	public $timestamps = false;
}