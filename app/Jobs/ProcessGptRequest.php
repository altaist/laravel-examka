<?php

namespace App\Jobs;

use App\Events\GptRequestCompleted;
use App\Events\GptRequestFailed;
use App\Models\GptRequest;
use App\Services\Gpt\GptServiceFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessGptRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected GptRequest $gptRequest
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GptServiceFactory $factory): void
    {
        try {
            // Обновляем статус на "processing"
            $this->gptRequest->update(['status' => 'processing']);

            // Получаем сервис из фабрики
            $service = $factory->make($this->gptRequest->metadata['service'] ?? 'openai');

            // Отправчляем запрос
            $result = $service->sendRequest(
                $this->gptRequest->prompt,
                [
                    'model' => $this->gptRequest->metadata['model'] ?? null,
                    'temperature' => $this->gptRequest->metadata['temperature'] ?? null,
                ]
            );

            // Обновляем запрос
            $this->gptRequest->update([
                'status' => 'completed',
                'response' => $result['content'],
                'metadata' => array_merge($this->gptRequest->metadata ?? [], [
                    'tokens_used' => $result['tokens_used'],
                    'model' => $result['model'],
                    'service' => $service->getName(),
                ]),
            ]);

            event(new GptRequestCompleted($this->gptRequest));
        } catch (\Exception $e) {
            Log::error('GPT Request failed', [
                'request_id' => $this->gptRequest->id,
                'error' => $e->getMessage(),
            ]);

            $this->gptRequest->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            event(new GptRequestFailed($this->gptRequest, $e->getMessage()));
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->gptRequest->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        event(new GptRequestFailed($this->gptRequest, $exception->getMessage()));
    }
} 