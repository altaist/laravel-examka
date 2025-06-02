<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'content' => [
                'sections' => [
                    [
                        'title' => $this->faker->sentence(),
                        'content' => $this->faker->paragraphs(3, true),
                    ],
                    [
                        'title' => $this->faker->sentence(),
                        'content' => $this->faker->paragraphs(2, true),
                    ],
                ],
            ],
        ];
    }
} 