<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'file_path',
        'file_name',
        'file_type',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
