<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_id',
        'status',
    ];

    // Usuario que envía la solicitud
    public function remitente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Usuario que recibe la solicitud
    public function destinatario()
    {
        return $this->belongsTo(User::class, 'contact_id');
    }
}
