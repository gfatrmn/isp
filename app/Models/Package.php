<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'speed_limit',
        'mikrotik_profile',
        'mikrotik_profile_isolated',
    ];

    // Relasi ke Customer (Pelanggan)
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
