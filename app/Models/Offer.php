<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'attachment',
        'sender_id',
    ];

    protected $casts = [
        'attachment' => 'array',
    ];

    // Offer belongs to a User (user_id is nullable)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->withDefault();
    }

    public function notifications()
{
    return $this->hasMany(Notification::class);
}

}
