<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
	protected $table = 'project';
	protected $fillable =  ['id', 'PCode', 'Project', 'Alamat', 'CCode'];
	public $timestamps = false;
}
