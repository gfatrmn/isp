<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Odp extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi ke Pelanggan (Satu ODP punya banyak Pelanggan)
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
