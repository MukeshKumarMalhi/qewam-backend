<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicedSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'session_id',
        'user_id'
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
