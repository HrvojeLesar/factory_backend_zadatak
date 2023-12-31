<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
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
        return $this->afterCreating(function (Category $category) {
            $created_at = fake()->dateTime();
            $langauges = Language::all("locale")->pluck("locale")->map(function ($locale) use ($category) {
                return [
                    "locale" => $locale,
                    "title" => $locale === "hr" ? sprintf("Naslov kategorije %d na HRV jeziku", $category->id)
                        : ($locale === "en" ? sprintf("Title for category %d", $category->id) : fake()->word())
                ];
            })->toArray();
            CategoryTranslation::factory()
                ->count(count($langauges))
                ->sequence(...$langauges)
                ->create([
                    "category_id" => $category->id,
                    "created_at" => $created_at,
                    "updated_at" => fake()->boolean(50) ? fake()->dateTimeBetween($created_at, "now") : null,
                    "deleted_at" => fake()->boolean(20) ? fake()->dateTimeBetween($created_at, "now") : null,
                ]);
        });
    }
}
