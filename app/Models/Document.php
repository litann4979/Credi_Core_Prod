<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'created_at',
        'updated_at',
    ];

    public function leadDocuments()
    {
        return $this->hasMany(LeadDocument::class);
    }

    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'lead_document')
                    ->withPivot(['filepath', 'upload_by', 'uploaded_at'])
                    ->withTimestamps();
    }

    public function userDocuments()
{
    return $this->hasMany(UserDocument::class);
}

}
