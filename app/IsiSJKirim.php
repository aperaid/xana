<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IsiSJKirim extends Model
{
	protected $table = 'isisjkirim';
	protected $fillable =  ['id', 'IsiSJKir', 'QKirim', 'QTertanda', 'QSisaKemInsert', 'QSisaKem', 'Purchase', 'SJKir'];
	public $timestamps = false;
}