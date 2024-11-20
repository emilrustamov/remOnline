<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_type',
        'is_supplier',
        'is_conflict',
        'first_name',
        'last_name',
        'contact_person',
        'address',
        'note',
        'status',
    ];

    // Связь с контактами клиента
    public function phones()
    {
        return $this->hasMany(ClientsPhones::class);
    }

    public function emails()
    {
        return $this->hasMany(ClientsEmails::class);
    }
}
