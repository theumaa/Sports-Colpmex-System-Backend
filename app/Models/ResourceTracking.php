<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ResourceTracking extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $hidden = [
        'trackable_id', 
        'trackable_type',
        'created_at',
        'updated_at'
    ];
    public function sports()
    {
        return $this->belongsTo(Sports::class, 'sport_id');
    }

    public function trackable():MorphTo{
       return $this->morphTo();
    }
    
}
