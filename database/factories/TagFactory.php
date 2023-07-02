<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }

    public function configure()
    {
        return $this->afterCreating(function (Tag $tag) {
            $created_at = fake()->dateTime();
            $langauges = Language::all("locale")->map(function ($locale) {
                return ["locale" => $locale, "title" => fake()->word()];
            })->toArray();
            TagTranslation::factory()
                ->count(count($langauges))
                ->sequence(...$langauges)
                ->create([
                    "tag_id" => $tag->id,
                    "created_at" => $created_at,
                    "updated_at" => fake()->boolean(50) ? fake()->dateTimeBetween($created_at, "now") : null,
                    "deleted_at" => fake()->boolean(20) ? fake()->dateTimeBetween($created_at, "now") : null,
                ]);
        });
    }
}
