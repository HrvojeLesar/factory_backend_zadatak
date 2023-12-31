<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Language;
use App\Models\Meal;
use App\Models\MealIngredients;
use App\Models\MealTags;
use App\Models\MealTranslation;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $created_at = fake()->dateTime();
        return [
            "created_at" => $created_at,
            "updated_at" => fake()->boolean(50) ? fake()->dateTimeBetween($created_at, "now") : null,
            "deleted_at" => fake()->boolean(20) ? fake()->dateTimeBetween($created_at, "now") : null,
            "category_id" => fake()->boolean(70) ? Category::all()->random()->id : null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Meal $meal) {
            $created_at = fake()->dateTime();
            $langauges = Language::all("locale")->pluck("locale")->map(function ($locale) use ($meal) {
                return [
                    "locale" => $locale,
                    "title" => $locale === "hr" ? sprintf("Naslov jela %d na HRV jeziku", $meal->id)
                        : ($locale === "en" ? sprintf("Title for meal %d", $meal->id) : fake()->word()),
                    "description" => $locale === "hr" ? sprintf("Opis jela %d", $meal->id)
                        : ($locale === "en" ? sprintf("Meal description %d", $meal->id) : fake()->text())
                ];
            })->toArray();
            MealTranslation::factory()
                ->count(count($langauges))
                ->sequence(...$langauges)
                ->create([
                    "meal_id" => $meal->id,
                    "created_at" => $created_at,
                    "updated_at" => fake()->boolean(50) ? fake()->dateTimeBetween($created_at, "now") : null,
                    "deleted_at" => fake()->boolean(20) ? fake()->dateTimeBetween($created_at, "now") : null,
                ]);

            try {
                MealIngredients::factory()
                    ->count(fake()->numberBetween(1, 15))
                    ->create([
                        "meal_id" => $meal->id,
                    ]);
            } catch (Exception) {
                //Ignores duplicate insertions
            }

            try {
                MealTags::factory()
                    ->count(fake()->numberBetween(1, 10))
                    ->create([
                        "meal_id" => $meal->id,
                    ]);
            } catch (Exception) {
                //Ignores duplicate insertions
            }
        });
    }
}
