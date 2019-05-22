<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todos extends Model
{
    //
    public $timestamps = false;
    public $table = "todos";
    protected $fillable = [
        'todo', 'deadline', 'duedate', 'status', 'userid',
    ];
}
