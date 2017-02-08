<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PO extends Model
{
	protected $table = 'po';
	protected $fillable =  ['id', 'POCode', 'Tgl', 'Discount', 'Periode', 'Catatan'];
	public $timestamps = false;
}