<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\IngredientTranslation;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
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
        return $this->afterCreating(function (Ingredient $ingredient) {
            $created_at = fake()->dateTime();
            $langauges = Language::all("locale")->map(function ($locale) {
                return ["locale" => $locale, "title" => fake()->word()];
            })->toArray();
            IngredientTranslation::factory()
                ->count(count($langauges))
                ->sequence(...$langauges)
                ->create([
                    "ingredient_id" => $ingredient->id,
                    "created_at" => $created_at,
                    "updated_at" => fake()->boolean(50) ? fake()->dateTimeBetween($created_at, "now") : null,
                    "deleted_at" => fake()->boolean(20) ? fake()->dateTimeBetween($created_at, "now") : null,
                ]);
        });
    }
}
