<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $table = 'customer';
	protected $fillable =  ['id', 'CCode', 'PPN', 'Company', 'Customer', 'CompAlamat', 'CompZip', 'CompKota', 'CompPhone', 'CompEmail', 'CustPhone', 'CustEmail', 'Fax', 'NPWP', 'Customer2', 'CustPhone2', 'CustEmail2', 'Customer3', 'CustPhone3', 'CustEmail3'];
	public $timestamps = false;
}
