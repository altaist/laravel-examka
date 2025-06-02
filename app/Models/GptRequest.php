<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GptRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_id',
        'prompt',
        'response',
        'status',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Получить документ, к которому относится запрос
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
} 