<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'access_users'];

    protected $casts = [
        'access_users' => 'array', // Автоматическая работа с JSON-данными
    ];

    public function hasAccess($userId)
    {
        return in_array($userId, $this->access_users ?? []);
    }

    public function addAccess($userId)
    {
        $users = $this->access_users ?? [];
        if (!in_array($userId, $users)) {
            $users[] = $userId;
            $this->update(['access_users' => $users]);
        }
    }

    public function removeAccess($userId)
    {
        $users = $this->access_users ?? [];
        $users = array_filter($users, fn($id) => $id != $userId);
        $this->update(['access_users' => $users]);
    }
    
}
