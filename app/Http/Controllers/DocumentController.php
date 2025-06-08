<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentController extends BaseController
{
    protected DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Создает новый документ
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Валидация входных данных
            $validator = Validator::make($request->all(), [
                'document_type_id' => 'required|exists:document_types,id',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'content' => 'nullable|array',
                'step' => 'nullable|string',
                'parts' => 'nullable|array',
                'parts.*.name' => 'required_with:parts|string|max:255',
                'parts.*.content' => 'nullable|array',
                'parts.*.order' => 'nullable|integer|min:0',
                'parts.*.metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Ошибка валидации',
                    $validator->errors()->toArray(),
                    422
                );
            }

            // Получаем тип документа
            $documentType = DocumentType::findOrFail($request->document_type_id);

            // Создаем документ
            $document = $this->documentService->create(
                $request->user(),
                $request->all(),
                $documentType
            );

            return $this->successResponse(
                'Документ успешно создан',
                $document,
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Ошибка при создании документа: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    /**
     * Получает список документов пользователя
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $documents = Document::with(['documentType', 'parts'])
                ->where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return $this->successResponse(
                'Список документов получен',
                $documents
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Ошибка при получении списка документов: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    /**
     * Получает информацию о конкретном документе
     *
     * @param Document $document
     * @return JsonResponse
     */
    public function show(Document $document): JsonResponse
    {
        try {
            // Проверяем, принадлежит ли документ текущему пользователю
            if ($document->user_id !== request()->user()->id) {
                return $this->errorResponse(
                    'Доступ запрещен',
                    [],
                    403
                );
            }

            $document->load(['documentType', 'parts']);

            return $this->successResponse(
                'Информация о документе получена',
                $document
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Ошибка при получении информации о документе: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    /**
     * Показывает форму создания документа
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        // Можно передать типы документов для выбора в форме
        $documentTypes = \App\Models\DocumentType::all();
        return \Inertia\Inertia::render('Document/Create', [
            'documentTypes' => $documentTypes,
        ]);
    }

    /**
     * Показывает форму создания части документа
     *
     * @param Document $document
     * @return \Inertia\Response
     */
    public function createPart(Document $document)
    {
        // Проверяем, принадлежит ли документ текущему пользователю
        if ($document->user_id !== request()->user()->id) {
            abort(403, 'Доступ запрещен');
        }

        return \Inertia\Inertia::render('Document/CreatePart', [
            'document' => $document
        ]);
    }

    /**
     * Создает новую часть документа
     *
     * @param Request $request
     * @param Document $document
     * @return JsonResponse
     */
    public function storePart(Request $request, Document $document): JsonResponse
    {
        try {
            // Проверяем, принадлежит ли документ текущему пользователю
            if ($document->user_id !== $request->user()->id) {
                return $this->errorResponse(
                    'Доступ запрещен',
                    [],
                    403
                );
            }

            // Валидация входных данных
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'content' => 'nullable|array',
                'index' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Ошибка валидации',
                    $validator->errors()->toArray(),
                    422
                );
            }

            // Создаем часть документа
            $part = $this->documentService->createPart($document, $request->all());

            return $this->successResponse(
                'Часть документа успешно создана',
                $part,
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Ошибка при создании части документа: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
} 