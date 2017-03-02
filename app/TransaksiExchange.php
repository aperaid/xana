<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaksiExchange extends Model
{
  protected $table = 'transaksiexchange';
	protected $fillable =  ['id', 'BExchange', 'QExchange', 'PExchange', 'Reference', 'Periode'];
	public $timestamps = false;
}
