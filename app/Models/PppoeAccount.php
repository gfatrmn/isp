<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PppoeAccount extends Model
{
    // Mengizinkan semua field (termasuk customer_id dan mikrotik_server_id) diisi lewat Controller
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function mikrotikServer()
    {
        return $this->belongsTo(MikrotikServer::class);
    }
}
