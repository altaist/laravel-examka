<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\GptRequest;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаем тестового пользователя
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Создаем несколько документов для тестового пользователя
        Document::factory()
            ->count(3)
            ->for($user)
            ->create()
            ->each(function (Document $document) {
                // Для каждого документа создаем несколько запросов GPT
                GptRequest::factory()
                    ->count(rand(2, 5))
                    ->for($document)
                    ->create();
            });

        // Создаем еще несколько пользователей с документами
        User::factory()
            ->count(5)
            ->create()
            ->each(function (User $user) {
                Document::factory()
                    ->count(rand(1, 3))
                    ->for($user)
                    ->create()
                    ->each(function (Document $document) {
                        GptRequest::factory()
                            ->count(rand(1, 3))
                            ->for($document)
                            ->create();
                    });
            });
    }
}
