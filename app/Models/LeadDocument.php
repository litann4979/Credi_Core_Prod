<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadDocument extends Model
{
   protected $table = 'lead_document';

    protected $fillable = [
        'lead_id',
        'document_id',
        'upload_by',
        'filepath',
        'uploaded_at',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'upload_by');
    }
}
