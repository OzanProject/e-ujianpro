<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemAnnouncement extends Model
{
    protected $fillable = ['title', 'content', 'type', 'is_active', 'created_at'];
}
