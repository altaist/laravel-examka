<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DocumentService extends BaseService
{
    /**
     * Создает новый документ
     *
     * @param User $user Пользователь, создающий документ
     * @param array $data Данные документа
     * @param DocumentType|int $documentType Тип документа или его ID
     * @return Document
     * @throws \Exception
     */
    public function create(User $user, array $data, DocumentType|int $documentType): Document
    {
        try {
            return DB::transaction(function () use ($user, $data, $documentType) {
                // Если передан ID типа документа, получаем модель
                if (is_int($documentType)) {
                    $documentType = DocumentType::findOrFail($documentType);
                }

                // Создаем документ
                $document = new Document([
                    'user_id' => $user->id,
                    'document_type_id' => $documentType->id,
                    'title' => $data['title'] ?? null,
                    'description' => $data['description'] ?? null,
                    'content' => $data['content'] ?? [],
                    'step' => $data['step'] ?? '0',
                ]);

                $document->save();

                // Если есть части документа, создаем их
                if (isset($data['parts']) && is_array($data['parts'])) {
                    foreach ($data['parts'] as $index => $partData) {
                        $document->parts()->create([
                            'name' => $partData['name'] ?? "Часть " . ($index + 1),
                            'content' => $partData['content'] ?? [],
                            'order' => $partData['order'] ?? $index,
                            'metadata' => $partData['metadata'] ?? null,
                        ]);
                    }
                }

                return $document->load(['documentType', 'parts']);
            });
        } catch (\Exception $e) {
            throw new \Exception('Ошибка при создании документа: ' . $e->getMessage());
        }
    }

    /**
     * Обновляет существующий документ
     *
     * @param Document $document Документ для обновления
     * @param array $data Новые данные
     * @return Document
     * @throws \Exception
     */
    public function update(Document $document, array $data): Document
    {
        try {
            return DB::transaction(function () use ($document, $data) {
                $document->update([
                    'title' => $data['title'] ?? $document->title,
                    'description' => $data['description'] ?? $document->description,
                    'content' => $data['content'] ?? $document->content,
                    'step' => $data['step'] ?? $document->step,
                ]);

                // Обновляем части документа, если они предоставлены
                if (isset($data['parts']) && is_array($data['parts'])) {
                    foreach ($data['parts'] as $partData) {
                        if (isset($partData['id'])) {
                            $part = $document->parts()->find($partData['id']);
                            if ($part) {
                                $part->update([
                                    'name' => $partData['name'] ?? $part->name,
                                    'content' => $partData['content'] ?? $part->content,
                                    'order' => $partData['order'] ?? $part->order,
                                    'metadata' => $partData['metadata'] ?? $part->metadata,
                                ]);
                            }
                        } else {
                            $document->parts()->create([
                                'name' => $partData['name'] ?? "Новая часть",
                                'content' => $partData['content'] ?? [],
                                'order' => $partData['order'] ?? $document->parts()->count(),
                                'metadata' => $partData['metadata'] ?? null,
                            ]);
                        }
                    }
                }

                return $document->load(['documentType', 'parts']);
            });
        } catch (\Exception $e) {
            throw new \Exception('Ошибка при обновлении документа: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет документ
     *
     * @param Document $document Документ для удаления
     * @return bool
     * @throws \Exception
     */
    public function delete(Document $document): bool
    {
        try {
            return DB::transaction(function () use ($document) {
                // Удаляем все связанные части документа
                $document->parts()->delete();
                
                // Удаляем все связанные запросы GPT
                $document->gptRequests()->delete();
                
                // Удаляем сам документ
                return $document->delete();
            });
        } catch (\Exception $e) {
            throw new \Exception('Ошибка при удалении документа: ' . $e->getMessage());
        }
    }

    /**
     * Создает новую часть документа
     *
     * @param Document $document Документ, к которому добавляется часть
     * @param array $data Данные части документа
     * @return \App\Models\DocumentPart
     * @throws \Exception
     */
    public function createPart(Document $document, array $data): \App\Models\DocumentPart
    {
        try {
            return DB::transaction(function () use ($document, $data) {
                // Если индекс не указан, используем максимальный индекс + 1
                if (!isset($data['index'])) {
                    $maxIndex = $document->parts()->max('index') ?? -1;
                    $data['index'] = $maxIndex + 1;
                }

                // Если указанный индекс уже существует, сдвигаем все последующие части
                if ($document->parts()->where('index', $data['index'])->exists()) {
                    $document->parts()
                        ->where('index', '>=', $data['index'])
                        ->increment('index');
                }

                $part = $document->parts()->create([
                    'name' => $data['name'] ?? 'Новая часть',
                    'content' => $data['content'] ?? [],
                    'index' => $data['index'],
                    'metadata' => $data['metadata'] ?? null,
                ]);

                return $part;
            });
        } catch (\Exception $e) {
            throw new \Exception('Ошибка при создании части документа: ' . $e->getMessage());
        }
    }
} 