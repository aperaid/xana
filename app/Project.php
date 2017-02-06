<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
	protected $table = 'project';
	protected $fillable =  ['id', 'PCode', 'Sales', 'Project', 'ProjAlamat', 'ProjZip', 'ProjKota', 'CCode'];
	public $timestamps = false;
}
