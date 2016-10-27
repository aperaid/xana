<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
	protected $table = 'periode';
	protected $fillable =  ['id', 'Periode', 'S', 'E', 'Quantity', 'IsiSJKir', 'SJKem', 'Reference', 'Purchase', 'Claim', 'Deletes'];
	public $timestamps = false;
}