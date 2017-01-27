<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $table = 'customer';
	protected $fillable =  ['id', 'CCode', 'Company', 'Customer', 'CompAlamat', 'CompZip', 'CompKota', 'CompPhone', 'CustPhone', 'Fax', 'NPWP', 'CompEmail', 'CustEmail', 'Customer2', 'CustPhone2', 'CustEmail2', 'Customer3', 'CustPhone3', 'CustEmail3'];
	public $timestamps = false;
}
