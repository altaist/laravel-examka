<?php

namespace Tests\Feature;

use App\Jobs\ProcessGptRequest;
use App\Models\Document;
use App\Models\GptRequest;
use App\Models\User;
use App\Services\Gpt\GptServiceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GptServicesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Document $document;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем тестового пользователя и документ
        $this->user = User::factory()->create();
        $this->document = Document::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Мокаем ответы от API
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Laravel is a web application framework with expressive, elegant syntax.',
                        ],
                    ],
                ],
                'usage' => [
                    'total_tokens' => 20,
                ],
            ], 200),
            'api.anthropic.com/*' => Http::response([
                'content' => [
                    [
                        'text' => 'Laravel is a modern PHP framework for web artisans.',
                    ],
                ],
                'usage' => [
                    'input_tokens' => 10,
                    'output_tokens' => 15,
                ],
            ], 200),
        ]);
    }

    /** @test */
    public function it_can_process_openai_request()
    {
        Queue::fake();

        // Создаем запрос к OpenAI
        $gptRequest = GptRequest::create([
            'document_id' => $this->document->id,
            'prompt' => 'What is Laravel?',
            'metadata' => [
                'service' => 'openai',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 0.7,
            ],
        ]);

        // Отправляем запрос в очередь
        ProcessGptRequest::dispatch($gptRequest);

        // Проверяем, что job был добавлен в очередь
        Queue::assertPushed(ProcessGptRequest::class, function ($job) use ($gptRequest) {
            return $job->gptRequest->id === $gptRequest->id;
        });

        // Запускаем job синхронно для тестирования
        $job = new ProcessGptRequest($gptRequest);
        $job->handle(app(GptServiceFactory::class));

        // Проверяем результат
        $gptRequest->refresh();
        $this->assertEquals('completed', $gptRequest->status);
        $this->assertEquals('Laravel is a web application framework with expressive, elegant syntax.', $gptRequest->response);
        $this->assertEquals('openai', $gptRequest->metadata['service']);
        $this->assertEquals('gpt-3.5-turbo', $gptRequest->metadata['model']);
        $this->assertEquals(20, $gptRequest->metadata['tokens_used']);

        // Проверяем, что запрос был отправлен с правильными параметрами
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/chat/completions' &&
                $request->hasHeader('Authorization', 'Bearer ' . config('services.openai.api_key')) &&
                $request['model'] === 'gpt-3.5-turbo' &&
                $request['temperature'] === 0.7;
        });
    }

    /** @test */
    public function it_can_process_anthropic_request()
    {
        Queue::fake();

        // Создаем запрос к Anthropic
        $gptRequest = GptRequest::create([
            'document_id' => $this->document->id,
            'prompt' => 'What is Laravel?',
            'metadata' => [
                'service' => 'anthropic',
                'model' => 'claude-3-haiku-20240307',
                'temperature' => 0.7,
            ],
        ]);

        // Отправляем запрос в очередь
        ProcessGptRequest::dispatch($gptRequest);

        // Проверяем, что job был добавлен в очередь
        Queue::assertPushed(ProcessGptRequest::class, function ($job) use ($gptRequest) {
            return $job->gptRequest->id === $gptRequest->id;
        });

        // Запускаем job синхронно для тестирования
        $job = new ProcessGptRequest($gptRequest);
        $job->handle(app(GptServiceFactory::class));

        // Проверяем результат
        $gptRequest->refresh();
        $this->assertEquals('completed', $gptRequest->status);
        $this->assertEquals('Laravel is a modern PHP framework for web artisans.', $gptRequest->response);
        $this->assertEquals('anthropic', $gptRequest->metadata['service']);
        $this->assertEquals('claude-3-haiku-20240307', $gptRequest->metadata['model']);
        $this->assertEquals(25, $gptRequest->metadata['tokens_used']);

        // Проверяем, что запрос был отправлен с правильными параметрами
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.anthropic.com/v1/messages' &&
                $request->hasHeader('x-api-key', config('services.anthropic.api_key')) &&
                $request['model'] === 'claude-3-haiku-20240307' &&
                $request['temperature'] === 0.7;
        });
    }

    /** @test */
    public function it_handles_failed_requests()
    {
        Queue::fake();

        // Создаем запрос с неверным сервисом
        $gptRequest = GptRequest::create([
            'document_id' => $this->document->id,
            'prompt' => 'What is Laravel?',
            'metadata' => [
                'service' => 'invalid_service',
                'model' => 'gpt-3.5-turbo',
            ],
        ]);

        // Отправляем запрос в очередь
        ProcessGptRequest::dispatch($gptRequest);

        // Запускаем job синхронно для тестирования
        $job = new ProcessGptRequest($gptRequest);
        $job->handle(app(GptServiceFactory::class));

        // Проверяем результат
        $gptRequest->refresh();
        $this->assertEquals('failed', $gptRequest->status);
        $this->assertNotNull($gptRequest->error_message);
        $this->assertStringContainsString('GPT service \'invalid_service\' not found', $gptRequest->error_message);
    }

    /** @test */
    public function it_handles_api_errors()
    {
        Queue::fake();

        // Мокаем ошибку API
        Http::fake([
            'api.openai.com/*' => Http::response([
                'error' => 'Invalid API key',
            ], 401),
        ]);

        // Создаем запрос к OpenAI
        $gptRequest = GptRequest::create([
            'document_id' => $this->document->id,
            'prompt' => 'What is Laravel?',
            'metadata' => [
                'service' => 'openai',
                'model' => 'gpt-3.5-turbo',
            ],
        ]);

        // Запускаем job синхронно для тестирования
        $job = new ProcessGptRequest($gptRequest);
        $job->handle(app(GptServiceFactory::class));

        // Проверяем результат
        $gptRequest->refresh();
        $this->assertEquals('failed', $gptRequest->status);
        $this->assertNotNull($gptRequest->error_message);
        $this->assertStringContainsString('OpenAI API request failed', $gptRequest->error_message);
    }
} 