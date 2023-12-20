<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandleFile extends Model
{
    use HasFactory;
    protected $table = 'handle_files';
    public $timestamps = true;
    protected $guarded = [];
}
