<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class saveInfo extends Model
{
    protected $table = 'reservation';
    protected $fillable = ['id','name','department','telephone','title','identity','place','begin','end','date'];
    public $timestamps = false;
}