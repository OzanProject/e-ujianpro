<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Multitenantable;

class Subject extends Model
{
    use Multitenantable;

    protected $fillable = ['code', 'name', 'created_by'];

    public function teachers()
    {
        return $this->belongsToMany(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
