<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'document_type_id',
        'step',
        'content',
        'title',
        'description',
    ];

    protected $casts = [
        'content' => 'array',
        'step' => 'string',
    ];

    /**
     * Получить пользователя, которому принадлежит документ
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить тип документа
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Получить все части документа
     */
    public function parts(): HasMany
    {
        return $this->hasMany(DocumentPart::class);
    }

    /**
     * Получить все запросы GPT для этого документа
     */
    public function gptRequests(): HasMany
    {
        return $this->hasMany(GptRequest::class);
    }
} 