<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaksiClaim extends Model
{
	protected $table = 'transaksiclaim';
	protected $fillable =  ['id', 'Claim', 'Tgl', 'QClaim', 'Amount', 'Purchase', 'Periode', 'IsiSJKir', 'PPN'];
	public $timestamps = false;
}