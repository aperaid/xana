<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaksiClaim extends Model
{
	protected $table = 'transaksiclaim';
	protected $fillable =  ['id', 'Claim', 'Tgl', 'QClaim', 'Amount', 'Discount', 'Purchase', 'Periode', 'IsiSJKir', 'Reference'];
	public $timestamps = false;
}