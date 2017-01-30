<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SJKirim extends Model
{
	protected $table = 'sjkirim';
	protected $fillable =  ['id', 'SJKir', 'Tgl', 'Reference', 'NoPolisi', 'Sopir', 'Kenek', 'FormMuat', 'Keterangan'];
	public $timestamps = false;
}