<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentPart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_id',
        'name',
        'content',
        'order',
        'metadata',
    ];

    protected $casts = [
        'content' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Получить документ, к которому относится часть
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Получить все запросы GPT для этой части документа
     */
    public function gptRequests(): HasMany
    {
        return $this->hasMany(GptRequest::class);
    }
} 