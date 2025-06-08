<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'mime_type',
        'description',
    ];

    /**
     * Получить все документы этого типа
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
} 