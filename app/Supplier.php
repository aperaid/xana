<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
  protected $table = 'supplier';
	protected $fillable =  ['id', 'SCode', 'Company', 'Supplier', 'CompAlamat', 'CompZip', 'CompKota', 'CompPhone', 'CompEmail', 'SupPhone', 'SupEmail', 'Fax', 'NPWP', 'Supplier2', 'SupPhone2', 'SupEmail2', 'Supplier3', 'SupPhone3', 'SupEmail3'];
	public $timestamps = false;
}
