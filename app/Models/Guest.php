<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
 
class Guest extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
