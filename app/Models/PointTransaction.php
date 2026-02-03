<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Institution; // Added this line

class PointTransaction extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'description', 'status', 'reference_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
