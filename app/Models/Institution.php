<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'dinas_name',
        'npsn',
        'email',
        'phone',
        'address',
        'city',
        'type',
        'head_master',
        'nip_head_master',
        'subdomain',
        'affiliate_code',
        'logo',
        'logo_kiri',
        'logo_kanan',
        'signature',
        'stamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(PointTransaction::class);
    }
}
