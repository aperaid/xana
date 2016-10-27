<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $table = 'customer';
	protected $fillable =  ['id', 'CCode', 'Company', 'Customer', 'Alamat', 'Zip', 'Kota', 'CompPhone', 'CustPhone', 'Fax', 'NPWP', 'CompEmail', 'CustEmail'];
	public $timestamps = false;
}
